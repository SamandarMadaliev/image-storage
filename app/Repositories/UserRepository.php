<?php

namespace App\Repositories;

use App\DTOs\Auth\StoreUserDto;
use App\Models\User;

class UserRepository
{
    public function create(StoreUserDto $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }
}
