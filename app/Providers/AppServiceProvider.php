<?php

namespace App\Providers;

use App\Support\RetryingFilesystem;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Avoid intermittent Windows "Access is denied" on Blade compiled views (rename).
        $this->app->singleton(Filesystem::class, function () {
            return new RetryingFilesystem;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
