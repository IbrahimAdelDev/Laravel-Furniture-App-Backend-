<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->registerUser($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'User has been registered successfully',
            'data' => $result
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginUser($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'User has been logged in successfully',
            'data' => $result
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logoutUser($request->user());

        return response()->json([
            'status' => 'success',
            'message' => 'User has been logged out successfully'
        ], 200);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword($request->user(), $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'User password has been changed successfully, please login again.'
        ], 200);
    }
}
