<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ImageMatchController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);
Route::patch('/tasks/{id}', [TaskController::class, 'markComplete']);

Route::post('/image-match', [ImageMatchController::class, 'match']);

Route::post('/register-face', [ImageMatchController::class, 'register']);
Route::post('/verify-face', [ImageMatchController::class, 'verify']);


Route::get('/test', function () {
    return response()->json(['msg' => 'API OK']);
});
