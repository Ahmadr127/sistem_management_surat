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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // 'admin' => \App\Http\Middleware\AdminMiddleware::class,
            // 'staff' => \App\Http\Middleware\StaffMiddleware::class,
            // 'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'checkUserStatus' => \App\Http\Middleware\CheckUserStatus::class,
            'checkRole' => \App\Http\Middleware\CheckRoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
