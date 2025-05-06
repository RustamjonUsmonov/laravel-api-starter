<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Handlers;

use App\Domains\Authorization\Commands\ResetPasswordCommand;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordHandler
{
    public function handle(ResetPasswordCommand $command)
    {
        return Password::reset(
            $command->dto->toArray(),
            static function ($user, $password): void {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );
    }
}
