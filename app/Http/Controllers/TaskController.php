<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Models\Task;
use App\Enum\TaskState;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;

class TaskController extends Controller
{
    /**
     * Get all tasks
     */
    public function index()
    {   
        $tasks = Task::paginate(20);

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a new task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'nullable|date|afterOrEqual:today',
            'user_id' => 'nullable|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $user = PersonalAccessToken::findToken($request->bearerToken())->tokenable()->first();

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'deadline' => $validated['deadline'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'created_by' => $user?->id,
            'project_id' => $validated['project_id'] ?? null,
        ]);

        return response()->json([
            'task' => $task,
            'message' => 'Task was successfully created.'
        ]);
    }

    /**
     * Get task with given id
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
     * Get all overdue tasks
     */
    public function overdue()
    {   
        $tasks = Task::wherePast('deadline')
            ->whereNot('state', TaskState::DONE)
            ->paginate(20);

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    /**
     * Update specific task
     */
    public function update(Request $request, $task_id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'deadline' => 'nullable|date|afterOrEqual:today',
            'user_id' => 'nullable|exists:users,id',
            'state' => Rule::enum(TaskState::class),
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $task = Task::findOrFail($task_id);

        $task->update($validated);

        TaskUpdated::dispatch($task);
        
        return response()->json([
            'task' => $task,
            'message' => 'Task was successfully updated.'
        ]);
    }

    /**
     * Delete task
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
