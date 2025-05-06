<?php

declare(strict_types=1);

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('allows a user to register successfully', function (): void {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'John Doe',
        'email' => 'john@gmail.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'message',
            'access_token',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@gmail.com',
    ]);
});

it('fails registration with invalid data', function (): void {
    $response = $this->postJson('/api/v1/register', [
        'email' => 'invalid-email',
        'password' => 'short',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['email', 'password'],
        ]);
});

it('fails registration with a duplicate email', function (): void {
    User::factory()->create(['email' => 'duplicate@example.com']);

    $response = $this->postJson('/api/v1/register', [
        'name' => 'Jane',
        'email' => 'duplicate@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['email'],
        ]);
});
