<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    // Login method
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'ok' => false,
                'err' => 'ERR_INVALID_CREDS',
                'msg' => 'incorrect username or password'
            ], 401);
        }

        $accessToken = $user->createToken('access_token', ['*'], now()->addSeconds(20))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['*'])->plainTextToken;

        return response()->json([
            'ok' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ],
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ]
        ]);
    }

    // Refresh access token
    public function refreshToken(Request $request)
    {
        $refreshToken = $request->bearerToken();

        $token = PersonalAccessToken::findToken($refreshToken);

        if (!$token || !$token->tokenable) {
            return response()->json([
                'ok' => false,
                'err' => 'ERR_INVALID_REFRESH_TOKEN',
                'msg' => 'invalid refresh token'
            ], 401);
        }

        $user = $token->tokenable;
        $accessToken = $user->createToken('access_token', ['*'], now()->addSeconds(20))->plainTextToken;

        return response()->json([
            'ok' => true,
            'data' => [
                'access_token' => $accessToken,
            ]
        ]);
    }
}

