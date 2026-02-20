<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageMatchController extends Controller
{
    public function match(Request $request)
    {
        // ✅ Validate input
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Reference image (already stored)
        $localImage = storage_path('app/reference/reference.jpg');

        if (!file_exists($localImage)) {
            return response()->json([
                'status' => false,
                'message' => 'Reference image not found'
            ], 500);
        }

        // ✅ Store uploaded image safely
        $uploadedImage = $request->file('image');

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $uploadedPath = $tempDir . '/' . Str::uuid() . '.jpg';
        $uploadedImage->move($tempDir, basename($uploadedPath));

        // ✅ Windows-compatible Python command
        $command = sprintf(
            'python %s %s %s',
            escapeshellarg(base_path('python/match.py')),
            escapeshellarg($localImage),
            escapeshellarg($uploadedPath)
        );

        // ✅ Execute
        $output = shell_exec($command);

        // ✅ Cleanup temp file
        if (file_exists($uploadedPath)) {
            unlink($uploadedPath);
        }

        if (!$output) {
            return response()->json([
                'status' => false,
                'message' => 'Python script failed to execute'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'result' => json_decode($output, true)
        ]);
    }



    //face register and match

//  public function register(Request $request)
//     {
//         $request->validate([
//             'name' => 'required',
//             'email' => 'required|email',
//             'image' => 'required|image'
//         ]);

//         $image = $request->file('image');

//         $imageData = base64_encode(file_get_contents($image));

//         $response = Http::post('http://127.0.0.1:5000/generate-embedding', [
//             'image' => $imageData
//         ]);

//         $responseData = $response->json();

//         if (!$response->successful() || !isset($responseData['status']) || !$responseData['status']) {
//             return response()->json([
//                 'message' => 'Face not detected'
//             ], 400);
//         }

//         $embedding = $responseData['embedding'];

//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'face_embedding' => json_encode($embedding)
//         ]);

//         return response()->json([
//             'message' => 'User Registered Successfully',
//             'user' => $user
//         ]);
//     }


    public function register(Request $request)
{
    $request->validate([
        'name'  => 'required',
        'email' => 'required|email',
        'image' => 'required|image'
    ]);

    // ============================
    // 1️⃣ SAVE IMAGE IN PUBLIC
    // ============================

    $image = $request->file('image');

    // Create unique filename
    $filename = time() . '_' . $image->getClientOriginalName();

    // Move file to public/faces
    $image->move(public_path('faces'), $filename);

    $imagePath = 'faces/' . $filename;

    // ============================
    // 2️⃣ GENERATE EMBEDDING
    // ============================

    $imageData = base64_encode(file_get_contents(public_path($imagePath)));

    $response = Http::timeout(30)->post('http://127.0.0.1:5000/generate-embedding', [
        'image' => $imageData
    ]);

    $responseData = $response->json();

    if (!$response->successful() || !isset($responseData['status']) || !$responseData['status']) {
        return response()->json([
            'message' => 'Face not detected'
        ], 400);
    }

    $embedding = $responseData['embedding'];

    // ============================
    // 3️⃣ SAVE USER
    // ============================

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'face_embedding' => json_encode($embedding),
        'image_path' => $imagePath
    ]);

    return response()->json([
        'message' => 'User Registered Successfully',
        'user' => $user,
        'image_url' => asset($imagePath)
    ]);
}
    // =============================
    // VERIFY
    // =============================
  
  
  public function verify(Request $request)
{
    $request->validate([
        'image' => 'required|image'
    ]);

    try {

        // 🔹 Convert image to base64
        $image = $request->file('image');
        $imageData = base64_encode(file_get_contents($image));

        // 🔹 Call Python API (ONLY ONCE)
        $response = Http::timeout(10)->post('http://127.0.0.1:5000/generate-embedding', [
            'image' => $imageData
        ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Face service unavailable'
            ], 500);
        }

        $responseData = $response->json();

        if (!isset($responseData['status']) || !$responseData['status']) {
            return response()->json([
                'message' => 'No face detected'
            ], 400);
        }

        $currentEmbedding = $responseData['embedding'];

        // 🔹 Load users (select only needed fields)
        $users = User::whereNotNull('face_embedding')
            ->select('id', 'name','email','image_path', 'face_embedding')
            ->get();

        $bestUser = null;
        $bestSimilarity = 0;

        foreach ($users as $user) {

            $storedEmbedding = json_decode($user->face_embedding, true);

            if (!is_array($storedEmbedding)) {
                continue;
            }

            if (count($storedEmbedding) !== count($currentEmbedding)) {
                continue;
            }

            // 🔹 Direct cosine similarity (NO HTTP CALL)
            $similarity = $this->cosineSimilarity($storedEmbedding, $currentEmbedding);

            if ($similarity > $bestSimilarity) {
                $bestSimilarity = $similarity;
                $bestUser = $user;
            }
        }

        // 🔹 Matching threshold
        if ($bestSimilarity > 0.65) {
            return response()->json([
                'match' => true,
                'user' => $bestUser,
                'similarity' => round($bestSimilarity, 4)
            ]);
        }

        return response()->json([
            'match' => false,
            'similarity' => round($bestSimilarity, 4)
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Verification failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

private function cosineSimilarity($a, $b)
{
    $dotProduct = 0.0;
    $normA = 0.0;
    $normB = 0.0;

    $length = count($a);

    for ($i = 0; $i < $length; $i++) {
        $dotProduct += $a[$i] * $b[$i];
        $normA += $a[$i] * $a[$i];
        $normB += $b[$i] * $b[$i];
    }

    if ($normA == 0 || $normB == 0) {
        return 0;
    }

    return $dotProduct / (sqrt($normA) * sqrt($normB));
}

  
  
  
  
  
  
  
//     public function verify(Request $request)
// {
//     $request->validate([
//         'image' => 'required|image'
//     ]);
//     $image = $request->file('image');
//     $imageData = base64_encode(file_get_contents($image));
//     $response = Http::post('http://127.0.0.1:5000/generate-embedding', [
//         'image' => $imageData
//     ]);
//     $responseData = $response->json();
//     if (!$response->successful() || !isset($responseData['status']) || !$responseData['status']) {
//         return response()->json([
//             'message' => 'No face detected'
//         ], 400);
//     }
//     $currentEmbedding = $responseData['embedding'];
//     $users = User::whereNotNull('face_embedding')->get();
//     $bestUser = null;
//     $bestSimilarity = 0;
//     foreach ($users as $user) {
//         $storedEmbedding = json_decode($user->face_embedding, true);
//         if (!is_array($storedEmbedding)) {
//             continue;
//         }
//         if (count($storedEmbedding) == 0 || count($currentEmbedding) == 0) {
//             continue;
//         }
//         $compare = Http::post('http://127.0.0.1:5000/compare', [
//             'emb1' => $storedEmbedding,
//             'emb2' => $currentEmbedding
//         ]);
//         $compareData = $compare->json();

//         if (!$compare->successful() || !isset($compareData['similarity'])) {
//             continue;
//         }
//         $similarity = $compareData['similarity'];
//         if ($similarity > $bestSimilarity) {
//             $bestSimilarity = $similarity;
//             $bestUser = $user;
//         }
//     }
//     if ($bestSimilarity > 0.65) {
//         return response()->json([
//             'match' => true,
//             'user' => $bestUser,
//             'similarity' => $bestSimilarity
//         ]);
//     }
//     return response()->json([
//         'match' => false,
//         'similarity' => $bestSimilarity,
//         'storeembading' => $storedEmbedding,
//         'currentembading' => $currentEmbedding
//     ]);
// }


}
