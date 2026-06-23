<?php

namespace App\Domains\Enrollments\Http\Controllers\Api;

use App\Domains\Enrollments\Http\Requests\Api\Enrollments\PromoteEnrollmentRequest;
use App\Domains\Enrollments\Http\Requests\Api\Enrollments\StoreEnrollmentRequest;
use App\Domains\Enrollments\Http\Requests\Api\Enrollments\TransferEnrollmentRequest;
use App\Domains\Enrollments\Http\Resources\EnrollmentResource;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Enrollments\Services\DeleteEnrollmentService;
use App\Domains\Enrollments\Services\PromoteEnrollmentService;
use App\Domains\Enrollments\Services\StoreEnrollmentService;
use App\Domains\Enrollments\Services\TransferEnrollmentService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use App\Domains\Students\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnrollmentController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private EnrollmentRepository $enrollmentRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Enrollment::class);

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');
        $academicPeriodId = $request->input('academicPeriodId');
        $sectionId = $request->input('sectionId');

        $enrollments = $this->enrollmentRepository->paginateForListing(
            $search,
            $status,
            $academicPeriodId,
            $sectionId,
            $this->resolvePerPage($request)
        );

        return $this->paginatedResponse($enrollments, EnrollmentResource::class);
    }

    public function show(Enrollment $enrollment): EnrollmentResource
    {
        $this->authorize('view', Enrollment::class);

        return new EnrollmentResource($enrollment->load(['student.user', 'section.academicPeriod']));
    }

    public function store(StoreEnrollmentRequest $request, StoreEnrollmentService $storeService, Student $student): JsonResponse
    {
        $this->authorize('create', Enrollment::class);

        $enrollment = $storeService->handle($student, $request->validated());

        return (new EnrollmentResource($enrollment->load(['student.user', 'section.academicPeriod'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(Enrollment $enrollment, DeleteEnrollmentService $deleteService): Response
    {
        $this->authorize('delete', $enrollment);

        $deleteService->handle($enrollment);

        return response()->noContent();
    }

    public function transfer(TransferEnrollmentRequest $request, TransferEnrollmentService $transferService, Enrollment $enrollment): EnrollmentResource
    {
        $this->authorize('transfer', $enrollment);

        $transferred = $transferService->handle($enrollment, $request->validated()['reason']);

        return new EnrollmentResource($transferred);
    }

    public function promote(PromoteEnrollmentRequest $request, PromoteEnrollmentService $promoteService, Enrollment $enrollment): EnrollmentResource
    {
        $this->authorize('promote', $enrollment);

        $newEnrollment = $promoteService->handle($enrollment, $request->validated()['section_id']);

        return new EnrollmentResource($newEnrollment);
    }
}
