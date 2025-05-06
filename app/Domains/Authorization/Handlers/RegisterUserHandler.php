<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Handlers;

use App\Domains\Authorization\Commands\RegisterUserCommand;
use App\Domains\Authorization\Contracts\UserRepositoryInterface;
use App\Domains\Authorization\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;

class RegisterUserHandler
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function handle(RegisterUserCommand $command): string
    {
        $dto = $command->dto;

        $user = $this->userRepository->createFromDto($dto);

        $user->assignRole(RoleEnum::USER->value);

        Auth::attempt([
            'email' => $dto->email,
            'password' => $dto->password,
        ]);

        return auth()->user()->createToken('access_token', ['refresh' => true])->plainTextToken;
    }
}
