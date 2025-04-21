<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthServices
{
    public static function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = JWTAuth::fromUser($user);
        return self::formatToken($token, $user);
    }

    public static function login(array $data): ?array
    {
        if (!$token = auth('api')->attempt($data)) {
            return null;
        }
        $user = auth('api')->user();
        return self::formatToken($token, $user);
    }

    public static function me(): ?User
    {
        return auth('api')->user();
    }

    public static function logout(): void
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            \Log::warning('Logout failed: ' . $e->getMessage());
        }
    }

    protected static function formatToken(string $token, User $user): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user->name,
        ];
    }
}
