<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case USER = 'user';

    public function fromValue($value): ?self
    {
        return match ($value) {
            'admin' => self::ADMIN,
            'moderator' => self::MODERATOR,
            'user' => self::USER,
            default => null,
        };
    }
}
