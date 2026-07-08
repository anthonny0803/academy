<?php

namespace Tests\Unit\Http;

use App\Domains\Shared\Contracts\RenderableDomainException;
use App\Domains\Shared\Http\ApiExceptionRenderer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ApiExceptionRendererTest extends TestCase
{
    public function test_validation_exception_maps_to_contract_envelope(): void
    {
        $exception = ValidationException::withMessages([
            'email' => 'El correo electrónico es obligatorio.',
        ]);

        $response = ApiExceptionRenderer::render($exception);
        $payload = $response->getData(true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('VALIDATION_ERROR', $payload['error']['code']);
        $this->assertSame('El correo electrónico es obligatorio.', $payload['error']['fields']['email']);
    }

    public function test_authentication_exception_maps_to_401(): void
    {
        $response = ApiExceptionRenderer::render(new AuthenticationException);
        $payload = $response->getData(true);

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('UNAUTHENTICATED', $payload['error']['code']);
    }

    public function test_authorization_exception_maps_to_403(): void
    {
        $response = ApiExceptionRenderer::render(new AuthorizationException);
        $payload = $response->getData(true);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('FORBIDDEN', $payload['error']['code']);
    }

    public function test_not_found_exceptions_map_to_404(): void
    {
        $model = ApiExceptionRenderer::render(new ModelNotFoundException);
        $http = ApiExceptionRenderer::render(new NotFoundHttpException);

        $this->assertSame(404, $model->getStatusCode());
        $this->assertSame(404, $http->getStatusCode());
        $this->assertSame('NOT_FOUND', $model->getData(true)['error']['code']);
    }

    public function test_unexpected_exception_hides_message_when_debug_disabled(): void
    {
        config(['app.debug' => false]);

        $response = ApiExceptionRenderer::render(new RuntimeException('boom'));
        $payload = $response->getData(true);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('SERVER_ERROR', $payload['error']['code']);
        $this->assertSame('Ocurrió un error interno en el servidor.', $payload['error']['message']);
    }

    public function test_unexpected_exception_exposes_message_when_debug_enabled(): void
    {
        config(['app.debug' => true]);

        $response = ApiExceptionRenderer::render(new RuntimeException('boom'));

        $this->assertSame('boom', $response->getData(true)['error']['message']);
    }

    public function test_renderable_domain_exception_maps_to_its_status_and_code(): void
    {
        $exception = new class('Regla de negocio violada.') extends RuntimeException implements RenderableDomainException
        {
            public function statusCode(): int
            {
                return 409;
            }

            public function errorCode(): string
            {
                return 'CUSTOM_CONFLICT';
            }
        };

        $response = ApiExceptionRenderer::render($exception);
        $payload = $response->getData(true);

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame('CUSTOM_CONFLICT', $payload['error']['code']);
        $this->assertSame('Regla de negocio violada.', $payload['error']['message']);
    }
}
