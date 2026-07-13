<?php

namespace App\Domains\Tenancy\Http\Controllers\Api;

use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use App\Domains\Tenancy\Http\Requests\Api\Tenants\StoreTenantRequest;
use App\Domains\Tenancy\Http\Requests\Api\Tenants\UpdateTenantRequest;
use App\Domains\Tenancy\Http\Resources\TenantResource;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Repositories\TenantRepository;
use App\Domains\Tenancy\Services\Tenants\DeleteTenantService;
use App\Domains\Tenancy\Services\Tenants\IssuePublicApiTokenService;
use App\Domains\Tenancy\Services\Tenants\StoreTenantService;
use App\Domains\Tenancy\Services\Tenants\UpdateTenantService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TenantController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private TenantRepository $tenantRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Tenant::class);

        $search = trim((string) $request->input('search', ''));
        $status = $request->has('status') ? (string) $request->input('status') : null;

        $tenants = $this->tenantRepository->paginateForListing($search, $status, $this->resolvePerPage($request));

        return $this->paginatedResponse($tenants, TenantResource::class);
    }

    public function show(Tenant $tenant): TenantResource
    {
        $this->authorize('viewAny', Tenant::class);

        return new TenantResource($tenant);
    }

    public function store(StoreTenantRequest $request, StoreTenantService $storeService): JsonResponse
    {
        $this->authorize('create', Tenant::class);

        $tenant = $storeService->handle($request->validated());

        return (new TenantResource($tenant))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateTenantRequest $request, UpdateTenantService $updateService, Tenant $tenant): TenantResource
    {
        $this->authorize('update', $tenant);

        $tenant = $updateService->handle($tenant, $request->validated());

        return new TenantResource($tenant);
    }

    public function destroy(Tenant $tenant, DeleteTenantService $deleteService): Response
    {
        $this->authorize('delete', $tenant);

        $deleteService->handle($tenant);

        return response()->noContent();
    }

    public function issuePublicToken(Tenant $tenant, IssuePublicApiTokenService $issueService): JsonResponse
    {
        $this->authorize('issueToken', $tenant);

        $token = $issueService->handle($tenant);

        return response()->json([
            'data' => [
                'token' => $token,
                'tokenType' => 'Bearer',
            ],
        ]);
    }
}
