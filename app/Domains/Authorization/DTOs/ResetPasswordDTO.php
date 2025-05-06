<?php

declare(strict_types=1);

namespace App\Domains\Authorization\DTOs;

final readonly class ResetPasswordDTO
{
    public string $email;
    public string $password;
    public string $password_confirmation;
    public string $token;

    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->password_confirmation = $data['password_confirmation'];
        $this->token = $data['token'];
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'token' => $this->token,
        ];
    }
}
