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
        // This validates the incoming request
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // This creates the user in the database
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        // This creates an API token for the new user
        $token = $user->createToken('myapptoken')->plainTextToken;

        // This sends back the user info and the token
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    /**
     * Log in a user.
     */
    public function login(Request $request)
    {
        // This validates the incoming request
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

        // This creates an API token for the logged-in user
        $token = $user->createToken('myapptoken')->plainTextToken;

        // This sends back the user info and the token
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    /**
     * Log out a user.
     */
    public function logout(Request $request)
    {
        // This deletes the user's API token
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out',
        ], 200);
    }
}
