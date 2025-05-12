<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureTaskPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = PersonalAccessToken::findToken($request->bearerToken())->tokenable()->first();

        $taskId = $request->id;

        $isUsersOwnTask = User::whereHas('tasks', function ($query) use ($user, $taskId) {
            $query->where('id', $taskId)
                ->where('user_id', $user->id);
        })->exists();

        if(!$isUsersOwnTask) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
