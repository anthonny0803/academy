<?php

use App\Domains\Shared\Contracts\RenderableDomainException;
use App\Domains\Shared\Http\ApiExceptionRenderer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Domains\Shared\Http\Middleware\PreventBackHistory::class,
            \App\Domains\Identity\Http\Middleware\CheckActiveUser::class,
            \App\Domains\Tenancy\Http\Middleware\ResolveTenant::class,
        ]);

        $middleware->alias([
            'public.token' => \App\Domains\Grades\Http\Middleware\ValidatePublicApiToken::class,
            'tenant.resolve' => \App\Domains\Tenancy\Http\Middleware\ResolveTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (\Throwable $e, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return ApiExceptionRenderer::render($e);
        });

        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            // The handler prepares exceptions before these callbacks run:
            // TokenMismatchException arrives wrapped in an HttpException(419)
            // and AuthorizationException as an AccessDeniedHttpException.
            if ($e->getPrevious() instanceof TokenMismatchException) {
                return redirect()->route('login')
                    ->with('error', 'Tu sesión ha expirado, inicia sesión nuevamente.');
            }

            if ($e instanceof RenderableDomainException
                || $e instanceof AccessDeniedHttpException) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }

            return null;
        });
    })->create();
