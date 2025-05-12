<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectTasksController extends Controller
{
    /**
     * Get all tasks for given project
     */
    public function __invoke($project_id)
    {
        try {
            $project = Project::where('id', $project_id)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found.'
            ], 404);
        }

        $tasks = Task::where('project_id', $project_id)
            ->paginate(20);

        return response()->json([
            'project' => $project,
            'tasks' => $tasks
        ]);
    }
}
