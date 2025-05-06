<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShouldAcceptJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');
        $response = $next($request);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
