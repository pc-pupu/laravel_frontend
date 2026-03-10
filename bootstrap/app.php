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
        // Security Headers - Apply globally
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        
        // Restrict HTTP Methods - Apply globally
        $middleware->append(\App\Http\Middleware\RestrictHttpMethodsMiddleware::class);
        
        // File Upload Validation - Apply to web routes
        $middleware->web(append: [
            \App\Http\Middleware\FileUploadValidationMiddleware::class,
            \App\Http\Middleware\InputSanitizationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            if (config('app.debug')) {
                return null;
            }
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $message = 'An error occurred. Please try again later.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'error' => 'Internal Server Error'], $status >= 400 ? $status : 500);
            }
            return null;
        });
    })->create();
