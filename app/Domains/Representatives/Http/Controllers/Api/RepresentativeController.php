<?php

namespace App\Domains\Representatives\Http\Controllers\Api;

use App\Domains\Representatives\Http\Requests\Api\Representatives\StoreRepresentativeRequest;
use App\Domains\Representatives\Http\Requests\Api\Representatives\UpdateRepresentativeRequest;
use App\Domains\Representatives\Http\Resources\RepresentativeResource;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Representatives\Repositories\RepresentativeRepository;
use App\Domains\Representatives\Services\StoreRepresentativeService;
use App\Domains\Representatives\Services\UpdateRepresentativeService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RepresentativeController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private RepresentativeRepository $representativeRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Representative::class);

        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('isActive') ? $request->boolean('isActive') : null;
        $hasStudents = $request->has('hasStudents') ? $request->boolean('hasStudents') : null;

        $representatives = $this->representativeRepository->paginateForListing(
            $search,
            $isActive,
            $hasStudents,
            $this->resolvePerPage($request)
        );

        return $this->paginatedResponse($representatives, RepresentativeResource::class);
    }

    public function show(Representative $representative): RepresentativeResource
    {
        $this->authorize('view', Representative::class);

        return new RepresentativeResource($representative->load('user'));
    }

    public function store(StoreRepresentativeRequest $request, StoreRepresentativeService $storeService): JsonResponse
    {
        $this->authorize('create', Representative::class);

        $representative = $storeService->handle($request->validated());

        return (new RepresentativeResource($representative))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(
        UpdateRepresentativeRequest $request,
        UpdateRepresentativeService $updateService,
        Representative $representative
    ): RepresentativeResource {
        $this->authorize('update', Representative::class);

        $result = $updateService->handle($representative, $request->validated());

        return new RepresentativeResource($result['representative']);
    }
}
