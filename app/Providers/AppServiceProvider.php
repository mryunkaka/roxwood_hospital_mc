<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Support\RetryingFilesystem;
use DateTimeZone;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Avoid intermittent Windows "Access is denied" on Blade compiled views (rename).
        $this->app->extend('files', function ($files) {
            return new RetryingFilesystem;
        });

        $this->app->singleton(Filesystem::class, function () {
            return $this->app->make('files');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // If cache driver is set to "database" but table is missing, fallback to file cache.
        // This prevents hard 500s on environments that didn't run the cache table migration.
        try {
            if (config('cache.default') === 'database') {
                $cacheTable = (string) (config('cache.stores.database.table') ?: 'cache');
                if ($cacheTable === '' || !Schema::hasTable($cacheTable)) {
                    config(['cache.default' => 'file']);
                }
            }
        } catch (\Throwable $e) {
            config(['cache.default' => 'file']);
        }

        $appSetting = null;
        try {
            if (Schema::hasTable('app_settings')) {
                $appSetting = Cache::remember('app_settings:current', now()->addHours(12), function () {
                    return AppSetting::query()->first();
                });
            }
        } catch (\Throwable $e) {
            $appSetting = null;
        }

        $rawName = (string) ($appSetting?->app_name ?: config('app.name', 'Roxwood Health Medical Center'));
        $parts = array_map('trim', explode('|', $rawName, 2));
        $appName = $parts[0] !== '' ? $parts[0] : (string) config('app.name', 'Roxwood Health Medical Center');
        $appTagline = $parts[1] ?? 'Health Medical';

        $appTimezone = (string) ($appSetting?->timezone ?: config('app.timezone', 'Asia/Jakarta'));
        if ($appTimezone === '' || ! in_array($appTimezone, DateTimeZone::listIdentifiers(), true)) {
            $appTimezone = (string) config('app.timezone', 'Asia/Jakarta');
        }
        config(['app.timezone' => $appTimezone]);
        @date_default_timezone_set($appTimezone);

        $fallbackLogo = asset('storage/logo%20rh%20copy.png');
        $appLogoUrl = $appSetting?->app_logo_path ? asset($appSetting->app_logo_path) : $fallbackLogo;

        View::share([
            'appSetting' => $appSetting,
            'appName' => $appName,
            'appTagline' => $appTagline,
            'appLogoUrl' => $appLogoUrl,
            'appTimezone' => $appTimezone,
        ]);
    }
}
