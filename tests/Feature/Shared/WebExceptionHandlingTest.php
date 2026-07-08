<?php

namespace Tests\Feature\Shared;

use App\Domains\Academics\Exceptions\TeacherNotQualifiedForSubjectException;
use App\Domains\Identity\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;
use Throwable;

class WebExceptionHandlingTest extends TestCase
{
    private const PREVIOUS_URL = '/dashboard';

    private function routeThrowing(Throwable $exception): string
    {
        Route::middleware('web')->get('/web-exception-test', fn () => throw $exception);

        return '/web-exception-test';
    }

    public function test_domain_exceptions_flash_their_message_and_redirect_back(): void
    {
        $exception = TeacherNotQualifiedForSubjectException::make();

        $response = $this->from(self::PREVIOUS_URL)->get($this->routeThrowing($exception));

        $response->assertRedirect(self::PREVIOUS_URL)
            ->assertSessionHas('error', $exception->getMessage());
    }

    public function test_exact_generic_exceptions_flash_their_message_and_redirect_back(): void
    {
        $response = $this->from(self::PREVIOUS_URL)
            ->get($this->routeThrowing(new Exception('No se puede eliminar este registro.')));

        $response->assertRedirect(self::PREVIOUS_URL)
            ->assertSessionHas('error', 'No se puede eliminar este registro.');
    }

    public function test_authorization_exceptions_flash_their_message_and_redirect_back(): void
    {
        $response = $this->from(self::PREVIOUS_URL)
            ->get($this->routeThrowing(new AuthorizationException('No tienes autorización para gestionar roles.')));

        $response->assertRedirect(self::PREVIOUS_URL)
            ->assertSessionHas('error', 'No tienes autorización para gestionar roles.');
    }

    public function test_infrastructure_exceptions_render_500_without_leaking_the_message(): void
    {
        config(['app.debug' => false]);

        $response = $this->from(self::PREVIOUS_URL)
            ->get($this->routeThrowing(new RuntimeException('internal secret detail')));

        $response->assertServerError()
            ->assertDontSee('internal secret detail')
            ->assertSessionMissing('error');
    }

    public function test_token_mismatch_redirects_to_login_with_expired_session_message(): void
    {
        $response = $this->from(self::PREVIOUS_URL)
            ->get($this->routeThrowing(new TokenMismatchException('CSRF token mismatch.')));

        $response->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Tu sesión ha expirado, inicia sesión nuevamente.');
    }

    public function test_missing_models_render_404_instead_of_redirecting_back(): void
    {
        $exception = (new ModelNotFoundException)->setModel(User::class);

        $response = $this->from(self::PREVIOUS_URL)->get($this->routeThrowing($exception));

        $response->assertNotFound();
    }
}
