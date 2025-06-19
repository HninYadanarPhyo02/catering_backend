<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(HandleCors::class);

         $middleware->validateCsrfTokens(except: [
            'customers/*',
            'customers',
            'adminLogin',
            'orders/*',
            'orders',
            'register/*',
            'register',
            'invoices/*',
            'invoices',
            'holidays/*',
            'holidays',
            'logout',
            'menus',
            'profile',
            'menus/*', // <-- This disables CSRF for all /customers/* routes
            'stripe/*',
            'attendance/*',
            'announcements',
            'announcements/*',
            'attendance',
            'http://example.com/foo/bar', // Only works if relative paths
            'http://example.com/foo/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
