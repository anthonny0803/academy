<?php

namespace App\Domains\Tenancy\Http\Middleware;

use App\Domains\Tenancy\Repositories\TenantRepository;
use App\Domains\Tenancy\Support\CurrentTenant;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTenantToken
{
    public function __construct(
        private CurrentTenant $currentTenant,
        private TenantRepository $tenantRepository
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return $this->unauthenticated();
        }

        $tenant = $this->tenantRepository->findByPublicApiTokenHash(hash('sha256', $token));

        if (! $tenant || ! $tenant->isActive()) {
            return $this->unauthenticated();
        }

        $this->currentTenant->set($tenant);

        return $next($request);
    }

    private function unauthenticated(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'UNAUTHENTICATED',
                'message' => 'Token de acceso inválido.',
            ],
        ], Response::HTTP_UNAUTHORIZED);
    }
}
