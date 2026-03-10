<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Register the standard web and console route files for the application.
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Respect proxy headers when the app runs behind Apache, Nginx, or a reverse proxy.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO,
        );

        // Short aliases make route middleware declarations easier to read in routes/web.php.
        $middleware->alias([
            'ojt.user' => \App\Http\Middleware\EnsureOjtUser::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'profile.completed' => \App\Http\Middleware\EnsureProfileCompleted::class,
            'approved.student' => \App\Http\Middleware\EnsureApprovedStudent::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
