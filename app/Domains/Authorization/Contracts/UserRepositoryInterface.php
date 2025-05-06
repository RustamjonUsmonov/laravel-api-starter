<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Contracts;

use App\Domains\Authorization\DTOs\RegisterUserDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function createFromDto(RegisterUserDTO $dto): User;
}
