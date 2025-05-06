<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Authorization\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshSanctumAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $token = $user?->currentAccessToken();

        // Если токен отсутствует
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Если токен истёк — пытаемся обновить
        if ($token->expires_at && $token->expires_at->isPast()) {
            // Удаляем старый токен
            $token->delete();

            // Генерируем новый
            try {
                $permissionNames = $user->getPermissionsViaRoles()->pluck('name')->toArray();
                $newToken = app(TokenService::class)->createToken($user, $permissionNames, now()->addHour());
            } catch (\Throwable) {
                return response()->json(['message' => 'Could not refresh token'], 500);
            }
            // Продолжаем цепочку и вставляем токен в заголовок
            $response = $next($request);
            $response->headers->set('Authorization', 'Bearer ' . $newToken);
            return $response;
        }

        return $next($request);
    }
}
