<?php

namespace App\Domains\Academics\Http\Controllers\Api;

use App\Domains\Academics\Http\Requests\Api\Sections\StoreSectionRequest;
use App\Domains\Academics\Http\Requests\Api\Sections\UpdateSectionRequest;
use App\Domains\Academics\Http\Resources\SectionResource;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Repositories\SectionRepository;
use App\Domains\Academics\Services\Sections\DeleteSectionService;
use App\Domains\Academics\Services\Sections\StoreSectionService;
use App\Domains\Academics\Services\Sections\UpdateSectionService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SectionController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private SectionRepository $sectionRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Section::class);

        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('isActive') ? $request->boolean('isActive') : null;
        $academicPeriodId = $request->input('academicPeriodId');

        $sections = $this->sectionRepository->paginateForListing($search, $isActive, $academicPeriodId, $this->resolvePerPage($request));

        return $this->paginatedResponse($sections, SectionResource::class);
    }

    public function show(Section $section): SectionResource
    {
        $this->authorize('view', $section);

        return new SectionResource($section);
    }

    public function store(StoreSectionRequest $request, StoreSectionService $storeService): JsonResponse
    {
        $this->authorize('create', Section::class);

        $section = $storeService->handle($request->validated());

        return (new SectionResource($section))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateSectionRequest $request, UpdateSectionService $updateService, Section $section): SectionResource
    {
        $this->authorize('update', $section);

        $section = $updateService->handle($section, $request->validated());

        return new SectionResource($section);
    }

    public function destroy(Section $section, DeleteSectionService $deleteService): Response
    {
        $this->authorize('delete', $section);

        $deleteService->handle($section);

        return response()->noContent();
    }
}
