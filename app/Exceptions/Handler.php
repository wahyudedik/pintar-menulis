<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render(Request $request, Throwable $exception)
    {
        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage() ?: 'An error occurred',
                'message' => $exception->getMessage() ?: 'An error occurred',
            ], $this->getStatusCode($exception));
        }

        return parent::render($request, $exception);
    }

    /**
     * Get the HTTP status code for the exception.
     */
    protected function getStatusCode(Throwable $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        if (method_exists($exception, 'status')) {
            return $exception->status();
        }

        return 500;
    }
}
