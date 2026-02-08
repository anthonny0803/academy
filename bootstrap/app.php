<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
            \App\Http\Middleware\CheckActiveUser::class,
        ]);

        $middleware->alias([
            'public.token' => \App\Http\Middleware\ValidatePublicApiToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (\Throwable $e, $request) {
            // Excluir errores de validación
            if ($e instanceof ValidationException) {
                return null;
            }

            if ($e instanceof TokenMismatchException) {
                return redirect()->route('login')
                    ->with('error', 'Tu sesión ha expirado, inicia sesión nuevamente.');
            }

            // Solo capturar otros errores
            if (!$request->expectsJson() && !app()->runningInConsole()) {
                if (!Auth::check()) {
                    return redirect()->route('login');
                }

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        });
    })->create();
