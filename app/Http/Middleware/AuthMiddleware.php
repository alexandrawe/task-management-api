<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Sanctum\PersonalAccessToken;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        
        $tokenIsValid = PersonalAccessToken::findToken($token);

        if(!$tokenIsValid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
