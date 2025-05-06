<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class LoggingMiddleware
{
    /**
     * Handle the command or query, logging it safely with masked sensitive data.
     */
    public function handle($commandOrQuery, Closure $next)
    {
        $class = $commandOrQuery::class;

        Log::channel('daily')->info("Dispatching: {$class}", [
            'commandOrQuery' => $this->maskSensitiveData($commandOrQuery),
        ]);

        $result = $next($commandOrQuery);

        Log::channel('daily')->info("Dispatched: {$class}", [
            'result' => $this->sanitizeResult($result),
        ]);

        return $result;
    }

    /**
     * Convert command/query to array and mask sensitive fields.
     */
    protected function maskSensitiveData($commandOrQuery): array
    {
        $data = json_decode(json_encode($commandOrQuery), true);

        $data = $this->maskNestedDTOs($commandOrQuery, $data);

        $sensitiveFields = $this->getSensitiveFields($commandOrQuery);

        array_walk_recursive($data, function (&$value, $key) use ($sensitiveFields): void {
            if (in_array($key, $sensitiveFields, true)) {
                $value = '****';
            }
        });

        return $data;
    }

    /**
     * Mask sensitive fields in nested DTOs.
     */
    protected function maskNestedDTOs($commandOrQuery, array $data): array
    {
        foreach (array_keys($data) as $key) {
            if (property_exists($commandOrQuery, $key) && is_object($commandOrQuery->$key)) {
                $data[$key] = $this->maskSensitiveData($commandOrQuery->$key);
            }

            //test
        }

        return $data;
    }

    /**
     * Get sensitive fields from command/query or config.
     */
    protected function getSensitiveFields($commandOrQuery): array
    {
        if (method_exists($commandOrQuery, 'sensitiveFields')) {
            return $commandOrQuery->sensitiveFields();
        }

        return config('logging.sensitive_fields', [
            'password',
            'access_token',
            'api_token',
            'hash',
            'email',
        ]);
    }

    /**
     * Sanitize the result to avoid logging sensitive data.
     */
    protected function sanitizeResult($result)
    {
        static $resultCache = [];

        $hash = is_object($result) ? spl_object_hash($result) : md5(serialize($result));
        if (isset($resultCache[$hash])) {
            return $resultCache[$hash];
        }

        if (is_string($result) && preg_match('/^\d+\|[a-zA-Z0-9]{32,}$/', $result)) {
            return $resultCache[$hash] = '****';
        }

        if ($result instanceof Model) {
            return $resultCache[$hash] = $this->maskSensitiveData($result);
        }

        if (is_object($result) && method_exists($result, 'sensitiveFields')) {
            return $resultCache[$hash] = $this->maskSensitiveData($result);
        }

        if (is_array($result)) {
            $sensitiveFields = config('logging.sensitive_fields', []);
            return $resultCache[$hash] = array_map(fn($value, $key) => in_array($key, $sensitiveFields, true) ? '****' : $value, $result, array_keys($result));
        }

        return $resultCache[$hash] = $result;
    }
}
