<?php

namespace App\Domains\Academics\Http\Controllers\Api;

use App\Domains\Academics\Http\Requests\Api\Teachers\StoreTeacherRequest;
use App\Domains\Academics\Http\Requests\Api\Teachers\UpdateTeacherRequest;
use App\Domains\Academics\Http\Resources\TeacherResource;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Repositories\TeacherRepository;
use App\Domains\Academics\Services\Teachers\StoreTeacherService;
use App\Domains\Academics\Services\Teachers\UpdateTeacherService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TeacherController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private TeacherRepository $teacherRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Teacher::class);

        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('isActive') ? $request->boolean('isActive') : null;

        $teachers = $this->teacherRepository->paginateForListing($search, $isActive, $this->resolvePerPage($request));

        return $this->paginatedResponse($teachers, TeacherResource::class);
    }

    public function show(Teacher $teacher): TeacherResource
    {
        $this->authorize('view', $teacher);

        return new TeacherResource($teacher->load('user'));
    }

    public function store(StoreTeacherRequest $request, StoreTeacherService $storeService): JsonResponse
    {
        $this->authorize('create', Teacher::class);

        $teacher = $storeService->handle($request->validated());

        return (new TeacherResource($teacher->load('user')))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateTeacherRequest $request, UpdateTeacherService $updateService, Teacher $teacher): TeacherResource
    {
        $this->authorize('update', $teacher);

        $teacher = $updateService->handle($teacher, $request->validated());

        return new TeacherResource($teacher);
    }
}
