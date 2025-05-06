<?php

declare(strict_types=1);

namespace Authorization;

use App\Domains\Authorization\Commands\RegisterUserCommand;
use App\Domains\Authorization\DTOs\RegisterUserDTO;

it('creates a command with correct DTO data', function (): void {
    // Arrange
    $dto = new RegisterUserDTO([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'secret123',
    ]);

    // Act
    $command = new RegisterUserCommand($dto);

    // Assert
    expect($command->dto)
        ->name->toBe('Test User')
        ->email->toBe('test@example.com')
        ->password->toBe('secret123');
});
