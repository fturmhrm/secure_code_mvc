<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirect authenticated users to dashboard when accessing guest routes (login, register)
        $middleware->redirectUsersTo('/dashboard');

        // Custom redirect for guests based on which lab they're accessing
        $middleware->redirectGuestsTo(function (Request $request) {
            // Redirect ke halaman login lab yang sesuai
            if ($request->routeIs('bac-lab.vulnerable.*')) {
                return route('bac-lab.vulnerable.login');
            }
            if ($request->routeIs('bac-lab.secure.*')) {
                return route('bac-lab.secure.login');
            }
            if ($request->routeIs('authorization-lab.*')) {
                return route('authorization-lab.login');
            }

            // Default redirect ke login biasa
            return route('login');
        });

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
