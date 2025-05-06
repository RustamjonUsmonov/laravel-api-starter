<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Domains\Authorization\Commands\VerifyEmailCommand;
use App\Domains\Authorization\Controllers\AuthController;
use App\Domains\Authorization\DTOs\VerifyEmailDTO;
use App\Models\User;
use App\Support\PipelineBus;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    $this->artisan('migrate:fresh');
});

it('verifies email with valid command', function (): void {
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);
    $dto = new VerifyEmailDTO($user->id, sha1((string) $user->email));
    $command = new VerifyEmailCommand($dto);
    $bus = app(PipelineBus::class);

    $result = $bus->dispatch($command);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->hasVerifiedEmail())->toBeTrue()
        ->and(fn() => $this->assertNotNull(User::find($user->id)->email_verified_at));
});

it('fails to verify email with invalid hash', function (): void {
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);
    $dto = new VerifyEmailDTO($user->id, 'invalid-hash');
    $command = new VerifyEmailCommand($dto);
    $bus = app(PipelineBus::class);

    expect(fn() => $bus->dispatch($command))
        ->toThrow(ValidationException::class, 'Invalid verification link');
});

it('fails to verify already verified email', function (): void {
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);
    $dto = new VerifyEmailDTO($user->id, sha1((string) $user->email));
    $command = new VerifyEmailCommand($dto);
    $bus = app(PipelineBus::class);

    expect(fn() => $bus->dispatch($command))
        ->toThrow(ValidationException::class, 'Email already verified');
});

it('creates VerifyEmailDTO from valid array', function (): void {
    $dto = VerifyEmailDTO::fromArray(['id' => 1, 'hash' => 'abcd1234']);

    expect($dto->id)->toBe(1)
        ->and($dto->hash)->toBe('abcd1234');
});

it('fails to create VerifyEmailDTO with missing fields', function (): void {
    expect(fn(): VerifyEmailDTO => VerifyEmailDTO::fromArray(['id' => 1]))
        ->toThrow(\InvalidArgumentException::class, 'Missing required fields: id or hash');
});

it('verifies email via controller with valid data', function (): void {
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);
    $controller = app(AuthController::class);

    $response = $controller->verifyEmail(new Request(), $user->id, sha1((string) $user->email));

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData()->message)->toBe('Email verified successfully')
        ->and(fn() => $this->assertNotNull(User::find($user->id)->email_verified_at));
});

it('fails to verify email via controller with invalid hash', function (): void {
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);
    $controller = app(AuthController::class);

    $response = $controller->verifyEmail(new Request(), $user->id, 'invalid-hash');

    expect($response->getStatusCode())->toBe(400)
        ->and($response->getData()->message)->toBe('Invalid verification link');
});
