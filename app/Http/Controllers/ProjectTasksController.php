<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ProjectTasksController extends Controller
{
    /**
     * Get all tasks for given project
     */
    public function __invoke(Request $request, $project_id)
    {
        $authUser = PersonalAccessToken::findToken($request->bearerToken())
            ->tokenable()
            ->with('role')
            ->first();

        try {
            $project = Project::where('id', $project_id)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found.'
            ], 404);
        }

        $tasks = Task::when($authUser->role->name === 'user', function ($query) use ($authUser) {
                return $query->where('user_id', $authUser->id);
            })
            ->where('project_id', $project_id)
            ->paginate(20);

        return response()->json([
            'project' => $project,
            'tasks' => $tasks
        ]);
    }
}
