<?php

declare(strict_types=1);

namespace App\Domains\Authorization\DTOs;

final readonly class ForgetPasswordDTO
{
    public string $email;

    public function __construct(array $data)
    {
        $this->email = $data['email'];
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
        ];
    }
}
