<?php

declare(strict_types=1);

namespace App\Interfaces\Api\v1\Routes;

use App\Domains\Authorization\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([], function (): void {
    // Authentication routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Password management
    Route::post('/forgot-password', [AuthController::class, 'forget']);
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.reset');
    Route::post('/update-password', [AuthController::class, 'updatePassword'])
        ->middleware(['auth:sanctum', 'token.expires'])
        ->name('password.update');

    // Token management
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('refresh.sanctum');
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'token.expires']);

    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify')
        ->middleware('signed');
});
