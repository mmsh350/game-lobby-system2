<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',

        ]);

        $user = User::create([
            'username' => $request->username,
        ]);

        // Create Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Create Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
