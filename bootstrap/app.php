<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function(HttpExceptionInterface $e, $request) {
            if($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMEssage()
                ], $e->getStatusCode());
            }

            return response()->view('exception.custom', [
                'code' => $e->getStatusCode(),
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->view('exception.expired-session', [
                'code' => 401,
                'message' => 'Your session expired. Please log in again.'
            ]);
        });
    })->create();
