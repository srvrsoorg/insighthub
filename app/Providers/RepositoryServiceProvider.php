<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\SyncPermissionInterface;
use App\Repositories\SyncPermissionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SyncPermissionInterface::class, SyncPermissionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
