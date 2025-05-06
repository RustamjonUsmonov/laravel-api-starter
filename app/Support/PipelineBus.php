<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Pipeline\Pipeline;

class PipelineBus implements BusInterface
{
    protected array $handlers;
    protected array $middleware;

    public function __construct(protected Pipeline $pipeline)
    {
        $this->handlers = config('cqrs.handlers', []);
        $this->middleware = config('cqrs.middleware', []);
    }

    public function withMiddleware(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function dispatch($commandOrQuery)
    {
        $commandClass = $commandOrQuery::class;
        if (!isset($this->handlers[$commandClass])) {
            throw new \InvalidArgumentException("No handler registered for {$commandClass}");
        }

        $handlerClass = $this->handlers[$commandClass];
        try {
            return $this->pipeline
                ->send($commandOrQuery)
                ->through($this->middleware)
                ->then(function ($commandOrQuery) use ($handlerClass) {
                    $handler = app($handlerClass);
                    return $handler->handle($commandOrQuery);
                });
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \DomainException('Duplicate entry detected', 422, $e);
            }
            throw $e;
        }
    }
}
