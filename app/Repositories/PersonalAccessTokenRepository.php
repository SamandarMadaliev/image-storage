<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenRepository
{
    public function create(User $user, string $name): string
    {
        return $user->createToken($name)->plainTextToken;
    }

    public function delete(PersonalAccessToken $token): void
    {
        $token->delete();
    }
}
