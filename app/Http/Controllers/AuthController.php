<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user_email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $loginCredentials = [
            'email' => $credentials['user_email'],
            'password' => $credentials['password']
        ];

        $user = User::where('email', $loginCredentials['email'])->first();

        if (!$user || !Hash::check($loginCredentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Неверные учетные данные'
            ], 401);
        }
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Вы успешно вошли в систему.',
            'tfa_authorized' => 1,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'You have successfully logged out.'
        ]);
    }
}
