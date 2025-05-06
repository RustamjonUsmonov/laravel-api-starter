<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Commands;

use App\Models\User;

class LogoutUserCommand
{
    public function __construct(public readonly User $user)
    {
    }

    public function sensitiveFields(): array
    {
        return ['password', 'remember_token'];
    }
}
