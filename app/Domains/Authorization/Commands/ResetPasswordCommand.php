<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Commands;

use App\Domains\Authorization\DTOs\ResetPasswordDTO;

class ResetPasswordCommand
{
    public function __construct(public ResetPasswordDTO $dto)
    {
    }

    public function sensitiveFields(): array
    {
        return ['password', 'password_confirmation'];
    }
}
