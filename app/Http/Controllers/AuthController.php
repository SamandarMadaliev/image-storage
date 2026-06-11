<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\LoginDto;
use App\DTOs\Auth\StoreUserDto;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    /**
     * Register a new user account.
     *
     * Returns a Sanctum API token to use for authenticated requests.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            StoreUserDto::fromArray($request->validated()),
        );

        return response()->json($result->toArray(), Response::HTTP_CREATED);
    }

    /**
     * Login with email and password.
     *
     * Returns a Sanctum API token to use for authenticated requests.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            LoginDto::fromArray($request->validated()),
        );

        return response()->json($result->toArray());
    }

    /**
     * Logout and revoke the current API token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get the authenticated user profile.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
