<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\TaskState;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;

use function Illuminate\Log\log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = PersonalAccessToken::findToken($request->bearerToken())->tokenable()->first();

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'created_by' => $user?->id,
        ]);

        return response()->json([
            'task' => $task,
            'message' => 'Task was successfully created.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($task_id)
    {
        $task = Task::find($task_id);

        if(!$task) {
            return response()->json([
                'message' => 'Task not found.'
            ], 404);
        }

        return response()->json([
            'task' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $task_id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'state' => Rule::enum(TaskState::class),
        ]);

        $task = Task::findOrFail($task_id);

        $task->update($validated);
        
        return response()->json([
            'task' => $task,
            'message' => 'Task was successfully updated.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($task_id)
    {
        $task = Task::find($task_id);

        if(!$task) {
            return response()->json([
                'message' => 'Task not found.'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'task_id' => $task_id,
            'message' => 'Task was successfully deleted.',
        ]);
    }
}
