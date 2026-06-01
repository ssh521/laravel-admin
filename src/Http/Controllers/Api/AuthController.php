<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Http\Resources\UserResource;
use Ssh521\LaravelAdmin\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request): JsonResponse
    {
        $result = $this->authService->login([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        return response()->json([
            'data' => [
                'user' => new UserResource($result['user']->load(['roles', 'permissions'])),
                'token' => $result['token'],
            ],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
