<?php

namespace App\Domains\Shared\Http;

use App\Domains\Shared\Contracts\RenderableDomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionRenderer
{
    public static function render(Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return self::validation($e);
        }

        if ($e instanceof AuthenticationException) {
            return self::error(401, 'UNAUTHENTICATED', 'No estás autenticado.');
        }

        if ($e instanceof AuthorizationException) {
            return self::error(403, 'FORBIDDEN', 'No tienes permiso para realizar esta acción.');
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return self::error(404, 'NOT_FOUND', 'El recurso solicitado no existe.');
        }

        if ($e instanceof RenderableDomainException) {
            return self::error($e->statusCode(), $e->errorCode(), $e->getMessage());
        }

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $code = $status === 403 ? 'FORBIDDEN' : 'HTTP_ERROR';

            return self::error($status, $code, $e->getMessage() ?: 'No se pudo procesar la solicitud.');
        }

        $message = config('app.debug')
            ? $e->getMessage()
            : 'Ocurrió un error interno en el servidor.';

        return self::error(500, 'SERVER_ERROR', $message);
    }

    private static function validation(ValidationException $e): JsonResponse
    {
        $fields = collect($e->errors())
            ->map(fn (array $messages) => $messages[0])
            ->all();

        return response()->json([
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Los datos proporcionados no son válidos.',
                'fields' => $fields,
            ],
        ], 422);
    }

    private static function error(int $status, string $code, string $message): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], $status);
    }
}
