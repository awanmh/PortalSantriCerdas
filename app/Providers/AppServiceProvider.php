<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Use this method to bind services into the container.
     */
    public function register(): void
    {
        // Contoh: $this->app->singleton(SomeService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * This method is called after all other services have been registered,
     * ideal for performing actions during app booting, like prefetching assets.
     */
    public function boot(): void
    {
        // Prefetch Vite assets to improve page load performance
        Vite::prefetch(concurrency: 3);
    }
}
