<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:128',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = Str::random(64);
        $user->api_token = hash('sha256', $token);
        $user->save();

        return response()->json([
            'accessToken' => $token,
            'user' => [
                'id' => (string) $user->_id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ],
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $newToken = Str::random(64);
        $user->api_token = hash('sha256', $newToken);
        $user->save();

        return response()->json(['accessToken' => $newToken]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $this->getAuthUser($request);
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json(['message' => 'Logged out']);
    }

    public static function getAuthUser(Request $request): ?User
    {
        $header = $request->header('Authorization');
        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = substr($header, 7);
        return User::where('api_token', hash('sha256', $token))->first();
    }
}
