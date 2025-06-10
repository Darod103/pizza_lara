<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Services\Api\AuthServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $userData = $request->validated();
        $response = AuthServices::register($userData);
        return response()->json($response, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $response = AuthServices::login($credentials);
        return response()->json($response, 201);
    }

    public function me(): JsonResponse
    {
        return response()->json(AuthServices::me());
    }

    public function logout(): JsonResponse
    {
        AuthServices::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
