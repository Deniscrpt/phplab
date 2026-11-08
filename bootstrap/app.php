<?php

declare(strict_types=1);

use App\Http\Middleware\CheckAuthorRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Аліас middleware для маршруту створення допису
        $middleware->alias([
            'post.access' => CheckAuthorRole::class,
        ]);

        // Тимчасово: тестування POST через PowerShell/cURL без CSRF-кукі
        $middleware->validateCsrfTokens(except: [
            'posts',
            'posts/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
