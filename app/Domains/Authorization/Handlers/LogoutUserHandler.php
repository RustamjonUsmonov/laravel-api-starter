<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Handlers;

use App\Domains\Authorization\Commands\LogoutUserCommand;
use App\Domains\Authorization\Services\TokenService;

class LogoutUserHandler
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    public function handle(LogoutUserCommand $command): bool
    {
        return $this->tokenService->deleteToken($command->user);
    }
}
