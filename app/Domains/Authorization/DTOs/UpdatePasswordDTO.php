<?php

declare(strict_types=1);

namespace App\Domains\Authorization\DTOs;

final readonly class UpdatePasswordDTO
{
    public string $current_password;
    public string $new_password;

    public function __construct(array $data)
    {
        $this->current_password = $data['current_password'];
        $this->new_password = $data['new_password'];
    }

    public function toArray(): array
    {
        return [
            'new_password' => $this->new_password,
            'current_password' => $this->current_password,
        ];
    }
}
