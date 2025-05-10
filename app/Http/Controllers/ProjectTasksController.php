<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
            ->with('tasks')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found.'
            ], 404);
        }

        return response()->json([
            'project' => $project,
        ]);
    }
}
