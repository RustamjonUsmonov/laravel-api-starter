<?php

declare(strict_types=1);

use App\Http\Middleware\LoggingMiddleware;
use App\Domains\Authorization\Commands\{ForgetPasswordCommand,
    LoginUserCommand,
    LogoutUserCommand,
    RegisterUserCommand,
    ResetPasswordCommand,
    UpdatePasswordCommand,
    VerifyEmailCommand};
use App\Domains\Authorization\Handlers\{ForgetPasswordHandler,
    LoginUserHandler,
    LogoutUserHandler,
    RegisterUserHandler,
    ResetPasswordHandler,
    UpdatePasswordHandler,
    VerifyEmailCommandHandler};

return [
    'handlers' => [
        ForgetPasswordCommand::class => ForgetPasswordHandler::class,
        LoginUserCommand::class => LoginUserHandler::class,
        LogoutUserCommand::class => LogoutUserHandler::class,
        RegisterUserCommand::class => RegisterUserHandler::class,
        ResetPasswordCommand::class => ResetPasswordHandler::class,
        UpdatePasswordCommand::class => UpdatePasswordHandler::class,
        VerifyEmailCommand::class => VerifyEmailCommandHandler::class,
    ],
    'middleware' => [
        LoggingMiddleware::class,
    ],
];
