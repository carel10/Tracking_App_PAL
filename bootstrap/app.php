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
        // Register account status check middleware
        $middleware->alias([
            'account.status' => \App\Http\Middleware\CheckAccountStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Render 401 Unauthorized error page
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return response()->view('errors.401', [], 401);
        });

        // Render 403 Forbidden error page
        $exceptions->render(function (\Illuminate\Auth\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This action is unauthorized.'], 403);
            }
            return response()->view('errors.403', [], 403);
        });

        // Render 403 for HTTP Exception with 403 status
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Forbidden.'], 403);
                }
                return response()->view('errors.403', [], 403);
            }
            if ($e->getStatusCode() === 401) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthorized.'], 401);
                }
                return response()->view('errors.401', [], 401);
            }
        });
    })->create();
