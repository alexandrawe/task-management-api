<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class UserTasksController extends Controller
{
    /**
     * Get all tasks for given user
     */
    public function __invoke(Request $request, $user_id)
    {
        $authUser = PersonalAccessToken::findToken($request->bearerToken())
            ->tokenable()
            ->with('role')
            ->first();

        if(($authUser->id != $user_id) && ($authUser->role->name !== 'admin')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
            
        try {
            $user = User::where('id', $user_id)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $tasks = Task::where('user_id', $user_id)
            ->paginate(20);

        return response()->json([
            'user' => $user,
            'tasks' => $tasks,
        ]);
    }
}
