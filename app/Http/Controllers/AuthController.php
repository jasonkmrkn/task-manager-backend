<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate incoming request
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Create new user
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        // API token for new user
        $token = $user->createToken('myapptoken')->plainTextToken;

        // Send back user info and API token
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    // Login
    public function login(Request $request)
    {
        // Validate request
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials',
            ], 401);
        }

        // Create token for user
        $token = $user->createToken('myapptoken')->plainTextToken;

        // Send back user info and API token
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    // Logout
    public function logout(Request $request)
    {
        // delete the user token
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out',
        ], 200);
    }
}
