<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Commands;

use App\Domains\Authorization\DTOs\LoginUserDTO;

class LoginUserCommand
{
    public function __construct(public LoginUserDTO $dto)
    {
    }

    public function sensitiveFields(): array
    {
        return ['password', 'access_token'];
    }
}
