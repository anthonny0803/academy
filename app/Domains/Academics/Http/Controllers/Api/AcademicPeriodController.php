<?php

namespace App\Domains\Academics\Http\Controllers\Api;

use App\Domains\Academics\Http\Requests\Api\AcademicPeriods\StoreAcademicPeriodRequest;
use App\Domains\Academics\Http\Requests\Api\AcademicPeriods\UpdateAcademicPeriodRequest;
use App\Domains\Academics\Http\Resources\AcademicPeriodResource;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Repositories\AcademicPeriodRepository;
use App\Domains\Academics\Services\AcademicPeriods\DeleteAcademicPeriodService;
use App\Domains\Academics\Services\AcademicPeriods\StoreAcademicPeriodService;
use App\Domains\Academics\Services\AcademicPeriods\UpdateAcademicPeriodService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AcademicPeriodController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private AcademicPeriodRepository $academicPeriodRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AcademicPeriod::class);

        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('isActive') ? $request->boolean('isActive') : null;

        $academicPeriods = $this->academicPeriodRepository->paginateForListing($search, $isActive, $this->resolvePerPage($request));

        return $this->paginatedResponse($academicPeriods, AcademicPeriodResource::class);
    }

    public function show(AcademicPeriod $academicPeriod): AcademicPeriodResource
    {
        $this->authorize('view', $academicPeriod);

        return new AcademicPeriodResource($academicPeriod);
    }

    public function store(StoreAcademicPeriodRequest $request, StoreAcademicPeriodService $storeService): JsonResponse
    {
        $this->authorize('create', AcademicPeriod::class);

        $academicPeriod = $storeService->handle($request->validated());

        return (new AcademicPeriodResource($academicPeriod))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateAcademicPeriodRequest $request, UpdateAcademicPeriodService $updateService, AcademicPeriod $academicPeriod): AcademicPeriodResource
    {
        $this->authorize('update', $academicPeriod);

        $academicPeriod = $updateService->handle($academicPeriod, $request->validated());

        return new AcademicPeriodResource($academicPeriod);
    }

    public function destroy(AcademicPeriod $academicPeriod, DeleteAcademicPeriodService $deleteService): Response
    {
        $this->authorize('delete', $academicPeriod);

        $deleteService->handle($academicPeriod);

        return response()->noContent();
    }
}
