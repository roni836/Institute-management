<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ResolveDevice;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // make it run on every web request (like Kernel::$middlewareGroups['web'])
        $middleware->appendToGroup('web', [
            ResolveDevice::class,
        ]);

        // optional: give it a short alias for route usage
        $middleware->alias([
            'resolve.device' => ResolveDevice::class,
            'admin'=> AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
