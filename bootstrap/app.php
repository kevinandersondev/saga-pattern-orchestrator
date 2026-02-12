<?php

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
        // AQUI ESTÁ A MUDANÇA:
        $middleware->validateCsrfTokens(except: [
            'checkout', // Libera a rota /checkout da proteção CSRF
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();