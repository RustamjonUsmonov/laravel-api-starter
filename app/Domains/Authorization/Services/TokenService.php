<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class TokenService
{
    public function createToken(User $user, array $abilities = ['*'], ?Carbon $expiresAt = null)
    {
        $token = $user->createToken('access_token', $abilities);

        if ($expiresAt instanceof Carbon) {
            $token->accessToken->expires_at = $expiresAt;
            $token->accessToken->save();
        }

        return $token->plainTextToken;
    }

    public function deleteToken(User $user): bool
    {
        if ($user?->currentAccessToken()) {
            $user->currentAccessToken()->delete();
            return true;
        }
        return false;
    }
}
