<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EventService;

class ServiceLayerProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EventService::class, function ($app) {
            return new EventService();
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
