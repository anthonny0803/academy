<?php

namespace App\Domains\Academics\Http\Controllers\Api;

use App\Domains\Academics\Http\Requests\Api\Subjects\StoreSubjectRequest;
use App\Domains\Academics\Http\Requests\Api\Subjects\UpdateSubjectRequest;
use App\Domains\Academics\Http\Resources\SubjectResource;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Repositories\SubjectRepository;
use App\Domains\Academics\Services\Subjects\DeleteSubjectService;
use App\Domains\Academics\Services\Subjects\StoreSubjectService;
use App\Domains\Academics\Services\Subjects\UpdateSubjectService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private SubjectRepository $subjectRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Subject::class);

        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('isActive') ? $request->boolean('isActive') : null;

        $subjects = $this->subjectRepository->paginateForListing($search, $isActive, $this->resolvePerPage($request));

        return $this->paginatedResponse($subjects, SubjectResource::class);
    }

    public function show(Subject $subject): SubjectResource
    {
        $this->authorize('viewAny', Subject::class);

        return new SubjectResource($subject);
    }

    public function store(StoreSubjectRequest $request, StoreSubjectService $storeService): JsonResponse
    {
        $this->authorize('create', Subject::class);

        $subject = $storeService->handle($request->validated());

        return (new SubjectResource($subject))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateSubjectRequest $request, UpdateSubjectService $updateService, Subject $subject): SubjectResource
    {
        $this->authorize('update', $subject);

        $subject = $updateService->handle($subject, $request->validated());

        return new SubjectResource($subject);
    }

    public function destroy(Subject $subject, DeleteSubjectService $deleteService): Response
    {
        $this->authorize('delete', $subject);

        $deleteService->handle($subject);

        return response()->noContent();
    }
}
