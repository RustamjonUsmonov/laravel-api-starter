<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Commands;

use App\Domains\Authorization\DTOs\RegisterUserDTO;

class RegisterUserCommand
{

    public function __construct(public RegisterUserDTO $dto)
    {
    }

    public function sensitiveFields(): array
    {
        return ['password', 'access_token'];
    }
}
