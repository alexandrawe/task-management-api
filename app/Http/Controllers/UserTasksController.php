<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserTasksController extends Controller
{
    /**
     * Get all tasks for given user
     */
    public function __invoke($user_id)
    {
        try {
            $user = User::where('id', $user_id)
                ->with('tasks')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        return response()->json([
            'user' => $user,
        ]);
    }
}
