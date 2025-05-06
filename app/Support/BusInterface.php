<?php

declare(strict_types=1);

namespace App\Support;

interface BusInterface
{
    public function dispatch($commandOrQuery);
}
