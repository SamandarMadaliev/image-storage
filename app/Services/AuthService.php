<?php

namespace App\Services;

use App\DTOs\Auth\AuthResultDto;
use App\DTOs\Auth\LoginDto;
use App\DTOs\Auth\StoreUserDto;
use App\Models\User;
use App\Repositories\PersonalAccessTokenRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private PersonalAccessTokenRepository $tokenRepository,
    ) {}

    public function register(StoreUserDto $dto): AuthResultDto
    {
        $user = $this->userRepository->create($dto);
        $token = $this->tokenRepository->create($user, 'auth-token');

        return new AuthResultDto($user, $token);
    }

    public function login(LoginDto $dto): AuthResultDto
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if ($user === null || ! Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $this->tokenRepository->create($user, 'auth-token');

        return new AuthResultDto($user, $token);
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token !== null) {
            $this->tokenRepository->delete($token);
        }
    }
}
