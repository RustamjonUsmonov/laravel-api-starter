<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Commands;

use App\Domains\Authorization\DTOs\VerifyEmailDTO;

final readonly class VerifyEmailCommand
{
    public function __construct(
        public VerifyEmailDTO $verifyEmailDTO,
    )
    {
    }

    public function sensitiveFields(): array
    {
        return ['id', 'hash'];
    }
}
