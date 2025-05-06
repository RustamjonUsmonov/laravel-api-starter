<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Handlers;

use App\Domains\Authorization\Commands\ForgetPasswordCommand;
use Illuminate\Support\Facades\Password;

class ForgetPasswordHandler
{
    public function handle(ForgetPasswordCommand $command): string
    {
        return Password::sendResetLink(
            $command->dto->toArray()
        );
    }
}
