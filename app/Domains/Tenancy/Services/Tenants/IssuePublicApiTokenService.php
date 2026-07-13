<?php

namespace App\Domains\Tenancy\Services\Tenants;

use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Repositories\TenantRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IssuePublicApiTokenService
{
    private const TOKEN_LENGTH = 48;

    public function __construct(private TenantRepository $tenantRepository) {}

    public function handle(Tenant $tenant): string
    {
        $plainToken = Str::random(self::TOKEN_LENGTH);

        DB::transaction(function () use ($tenant, $plainToken) {
            $rotated = $tenant->public_api_token_hash !== null;

            $this->tenantRepository->updatePublicApiTokenHash($tenant, hash('sha256', $plainToken));

            Log::info('Public API token issued for tenant', [
                'tenant_id' => $tenant->id,
                'tenant_slug' => $tenant->slug,
                'rotated' => $rotated,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);
        });

        return $plainToken;
    }
}
