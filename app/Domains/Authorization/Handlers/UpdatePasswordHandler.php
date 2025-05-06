<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Handlers;

use App\Domains\Authorization\Commands\UpdatePasswordCommand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordHandler
{
    public function handle(UpdatePasswordCommand $command): bool
    {
        $dto = $command->dto;

        $user = Auth::user();

        if (Hash::check($dto->current_password, $user->password)) {
            $user->password = bcrypt($dto->new_password);
            $user->save();

            return true;
        }

        return false;
    }
}
