<?php

namespace App\Domains\Tenancy\Http\Middleware;

use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Repositories\TenantRepository;
use App\Domains\Tenancy\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResolveTenant
{
    public function __construct(
        private CurrentTenant $currentTenant,
        private TenantRepository $tenantRepository
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->resolveTenant($request);

        if ($tenant !== null) {
            $this->currentTenant->set($tenant);
        }

        return $next($request);
    }

    private function resolveTenant(Request $request): ?Tenant
    {
        $user = Auth::user();

        if ($user !== null) {
            return $user->tenant;
        }

        return $this->resolveFromSubdomain($request);
    }

    private function resolveFromSubdomain(Request $request): ?Tenant
    {
        $subdomain = $this->extractSubdomain($request->getHost());

        if ($subdomain === null) {
            return null;
        }

        return $this->tenantRepository->findBySlug($subdomain);
    }

    private function extractSubdomain(string $host): ?string
    {
        foreach (config('tenancy.central_domains') as $central) {
            if ($host === $central) {
                return null;
            }

            if (str_ends_with($host, '.'.$central)) {
                return substr($host, 0, -strlen('.'.$central));
            }
        }

        return null;
    }
}
