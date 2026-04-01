<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'     => \App\Http\Middleware\RoleMiddleware::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'feature'  => \App\Http\Middleware\CheckFeatureAccess::class,
            'ai.limit' => \App\Http\Middleware\SetAIExecutionLimit::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackFeatureUsage::class);
        // Exclude payment webhooks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhook/midtrans',
            'webhook/xendit',
            'webhook/whatsapp',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON for API requests — never leak internal error details
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                // Don't log or intercept standard HTTP exceptions (404, 403, etc.)
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'Not found.',
                    ], $e->getStatusCode());
                }

                \Log::error('Unhandled API exception: ' . $e->getMessage(), [
                    'url'   => $request->fullUrl(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.',
                ], 500);
            }
        });
    })->create();
