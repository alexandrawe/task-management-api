<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Log user in with email and password and return token
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc',
            'password' => 'required|string',
        ]);

        Auth::once([
            "email" => $validated['email'],
            "password" => $validated['password'],
        ]);
        
        if(Auth::check()) {
            $user = Auth::user();
            $token = $user->createToken('api-token');

            return response()->json([
                'token' => $token->plainTextToken,
            ]);
        }

        return response()->json([
            'message' => 'Wrong credentials.'
        ], 401);
    }
}
