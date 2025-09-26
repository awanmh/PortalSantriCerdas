<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Contoh: $this->app->singleton(SomeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prefetch Vite assets (hanya tersedia di Laravel 11+)
        if (method_exists(Vite::class, 'prefetch')) {
            Vite::prefetch(concurrency: 3);
        }

        // Set locale Carbon secara global
        Carbon::setLocale('id');
    }
}
