<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Fix for environments where OS-level variables exist but are empty.
 * Laravel's dotenv repository is immutable, so an empty (but set) APP_KEY from the OS
 * prevents `.env` from supplying the real key, causing `MissingAppKeyException`.
 */
foreach (['APP_KEY'] as $key) {
    $value = getenv($key);
    if ($value === '') {
        // Unset for this process so `.env` can populate it.
        @putenv($key);
        if (array_key_exists($key, $_ENV) && $_ENV[$key] === '') {
            unset($_ENV[$key]);
        }
        if (array_key_exists($key, $_SERVER) && $_SERVER[$key] === '') {
            unset($_SERVER[$key]);
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['auth' => \App\Http\Middleware\SimpleAuth::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
