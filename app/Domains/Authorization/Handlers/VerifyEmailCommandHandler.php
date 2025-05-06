<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Handlers;

use App\Domains\Authorization\Commands\VerifyEmailCommand;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class VerifyEmailCommandHandler
{
    public function handle(VerifyEmailCommand $command): User
    {
        $dto = $command->verifyEmailDTO;
        $user = User::findOrFail($dto->id);

        if (!hash_equals($dto->hash, sha1((string) $user->getEmailForVerification()))) {
            throw ValidationException::withMessages(['hash' => 'Invalid verification link']);
        }

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages(['email' => 'Email already verified']);
        }

        $user->markEmailAsVerified();

        return $user;
    }
}
