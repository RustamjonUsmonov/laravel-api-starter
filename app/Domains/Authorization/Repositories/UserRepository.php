<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Repositories;


use App\Domains\Authorization\Contracts\UserRepositoryInterface;
use App\Domains\Authorization\DTOs\RegisterUserDTO;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function createFromDto(RegisterUserDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => config('defaults.customer_phone'),
            'password' => bcrypt($dto->password),
        ]);
    }
}
