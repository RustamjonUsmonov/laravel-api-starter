<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Commands;

use App\Domains\Authorization\DTOs\ForgetPasswordDTO;

class ForgetPasswordCommand
{
    public function __construct(public ForgetPasswordDTO $dto)
    {
    }

    public function sensitiveFields(): array
    {
        return [];
    }
}
