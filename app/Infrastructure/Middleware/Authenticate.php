<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson() || $request->is('api/*') || $request->is('api/v1/*')) {
            return null;
        }

        return route('login');
    }
}
