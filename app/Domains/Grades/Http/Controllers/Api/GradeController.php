<?php

namespace App\Domains\Grades\Http\Controllers\Api;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Grades\Http\Requests\Api\Grades\BatchGradeRequest;
use App\Domains\Grades\Http\Requests\Api\Grades\StoreGradeRequest;
use App\Domains\Grades\Http\Requests\Api\Grades\UpdateGradeRequest;
use App\Domains\Grades\Http\Resources\GradeResource;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Repositories\GradeRepository;
use App\Domains\Grades\Services\Grades\BatchGradeService;
use App\Domains\Grades\Services\Grades\DeleteGradeService;
use App\Domains\Grades\Services\Grades\StoreGradeService;
use App\Domains\Grades\Services\Grades\UpdateGradeService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GradeController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private GradeRepository $gradeRepository,
        private EnrollmentRepository $enrollmentRepository
    ) {}

    public function index(Request $request, SectionSubjectTeacher $sectionSubjectTeacher): JsonResponse
    {
        $this->authorize('viewAny', Grade::class);

        $grades = $this->gradeRepository->paginateForAssignment(
            $sectionSubjectTeacher->id,
            $this->resolvePerPage($request)
        );

        return $this->paginatedResponse($grades, GradeResource::class);
    }

    public function show(Grade $grade): GradeResource
    {
        $this->authorize('view', $grade);

        return new GradeResource($grade->load(['enrollment.student.user', 'gradeColumn']));
    }

    public function store(StoreGradeRequest $request, StoreGradeService $storeService, GradeColumn $gradeColumn): JsonResponse
    {
        $data = $request->validated();
        $enrollment = $this->enrollmentRepository->findOrFail($data['enrollment_id']);

        $this->authorize('createForColumn', [Grade::class, $gradeColumn, $enrollment]);

        $grade = $storeService->handle($gradeColumn, $enrollment, $data);

        return (new GradeResource($grade))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function storeBatch(BatchGradeRequest $request, BatchGradeService $batchService, GradeColumn $gradeColumn): JsonResponse
    {
        $this->authorize('create', Grade::class);

        $results = $batchService->handle($gradeColumn, $request->validated()['grades']);

        return response()->json(['data' => $results]);
    }

    public function update(UpdateGradeRequest $request, UpdateGradeService $updateService, Grade $grade): GradeResource
    {
        $this->authorize('update', $grade);

        $grade = $updateService->handle($grade, $request->validated());

        return new GradeResource($grade);
    }

    public function destroy(Grade $grade, DeleteGradeService $deleteService): Response
    {
        $this->authorize('delete', $grade);

        $deleteService->handle($grade);

        return response()->noContent();
    }
}
