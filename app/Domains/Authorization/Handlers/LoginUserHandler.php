<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Handlers;

use App\Domains\Authorization\Commands\LoginUserCommand;
use App\Domains\Authorization\Services\TokenService;
use Illuminate\Support\Facades\Auth;

class LoginUserHandler
{
    public function __construct(protected TokenService $tokenService)
    {
    }

    public function handle(LoginUserCommand $command): ?array
    {
        $dto = $command->dto;

        if (!Auth::attempt($dto->toArray())) {
            return null;
        }

        $user = auth()->user();

        $expiresAt = now()->addMinutes(60);

        $token = $this->tokenService->createToken($user, $user->load('permissions')->permissions()->get()->toArray(), $expiresAt);

        return [
            'access_token' => $token
        ];
    }

}
