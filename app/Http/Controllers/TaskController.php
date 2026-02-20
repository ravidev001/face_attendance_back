<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskModel;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = TaskModel::all();

        return response()->json([
            'status' => true,
            'data' => $tasks
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
        public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'descruiption' => 'nullable|string',
            'is_completed' => 'required|boolean',
        ]);

        $task = TaskModel::create([
            'title' => $request->title,
            'descruiption' => $request->descruiption,
            'is_completed' => $request->is_completed,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $task = TaskModel::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'descruiption' => 'nullable|string',
            'is_completed' => 'sometimes|boolean',
        ]);

        $task->update($request->only([
            'title',
            'descruiption',
            'is_completed'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    public function markComplete($id) {
    // Find the task by its ID
    $task = TaskModel::find($id);

    // If the task is not found, return a 404 error response
    if (!$task) {
        return response()->json([
            'success' => false,
            'message' => 'Task not found'
        ], 404);
    }

    // Toggle the 'is_completed' status:
    // If it's true, it becomes false. If it's false, it becomes true.
    $task->update([
        'is_completed' => !$task->is_completed
    ]);

    // Return a success response with the updated task data
    return response()->json([
        'success' => true,
        'message' => 'Task status toggled',
        'data' => $task
    ]);
}


    //     public function markComplete($id)
    // {
    //     $task = TaskModel::find($id);

    //     if (!$task) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Task not found'
    //         ], 404);
    //     }

    //     $task->update([
    //         'is_completed' => true
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Task marked as completed',
    //         'data' => $task
    //     ]);
    // }

}
