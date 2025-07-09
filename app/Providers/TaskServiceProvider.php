<?php

namespace App\Providers;

use App\Services\TaskService;
use App\Services\TaskValidationService;
use Illuminate\Support\ServiceProvider;

class TaskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register TaskValidationService as singleton
        $this->app->singleton(TaskValidationService::class, function ($app) {
            return new TaskValidationService();
        });

        // Register TaskService
        $this->app->bind(TaskService::class, function ($app) {
            return new TaskService(
                $app->make(TaskValidationService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
