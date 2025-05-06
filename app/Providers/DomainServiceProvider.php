<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Authorization\Contracts\UserRepositoryInterface;
use App\Domains\Authorization\Repositories\UserRepository;
use App\Support\BusInterface;
use App\Support\PipelineBus;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(BusInterface::class, PipelineBus::class);
    }
}
