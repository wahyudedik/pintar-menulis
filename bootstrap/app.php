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
        $middleware->alias([
            'role'     => \App\Http\Middleware\RoleMiddleware::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
        // Exclude payment webhooks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhook/midtrans',
            'webhook/xendit',
            'webhook/whatsapp',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON for API requests
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage() ?: 'An error occurred',
                    'message' => $e->getMessage() ?: 'An error occurred',
                ], 500);
            }
        });
    })->create();
