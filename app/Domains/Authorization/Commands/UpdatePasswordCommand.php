<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Commands;

use App\Domains\Authorization\DTOs\UpdatePasswordDTO;

class UpdatePasswordCommand
{
    public function __construct(public UpdatePasswordDTO $dto)
    {
    }

    public function sensitiveFields(): array
    {
        return ['current_password', 'new_password'];
    }
}
