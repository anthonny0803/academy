<?php

namespace App\Domains\Students\Http\Controllers\Api;

use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use App\Domains\Students\Http\Requests\Api\Students\StoreStudentRequest;
use App\Domains\Students\Http\Requests\Api\Students\UpdateStudentRequest;
use App\Domains\Students\Http\Requests\Api\Students\WithdrawStudentRequest;
use App\Domains\Students\Http\Resources\StudentResource;
use App\Domains\Students\Models\Student;
use App\Domains\Students\Repositories\StudentRepository;
use App\Domains\Students\Services\StoreStudentService;
use App\Domains\Students\Services\UpdateStudentService;
use App\Domains\Students\Services\WithdrawStudentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private StudentRepository $studentRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Student::class);

        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('isActive') ? $request->boolean('isActive') : null;
        $academicPeriodId = $request->input('academicPeriodId');
        $sectionId = $request->input('sectionId');

        $students = $this->studentRepository->paginateForListing(
            $search,
            $isActive,
            $academicPeriodId,
            $sectionId,
            $this->resolvePerPage($request)
        );

        return $this->paginatedResponse($students, StudentResource::class);
    }

    public function show(Student $student): StudentResource
    {
        $this->authorize('view', Student::class);

        return new StudentResource($student->load(['user', 'representative.user']));
    }

    public function store(StoreStudentRequest $request, StoreStudentService $storeService, Representative $representative): JsonResponse
    {
        $this->authorize('create', Student::class);

        $student = $storeService->handle($representative, $request->validated());

        return (new StudentResource($student))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateStudentRequest $request, UpdateStudentService $updateService, Student $student): StudentResource
    {
        $this->authorize('update', Student::class);

        $result = $updateService->handle($student, $request->validated());

        return new StudentResource($result['student']->load('representative.user'));
    }

    public function withdraw(WithdrawStudentRequest $request, WithdrawStudentService $withdrawService, Student $student): StudentResource
    {
        $this->authorize('withdraw', $student);

        $result = $withdrawService->handle($student, $request->validated()['reason']);

        return new StudentResource($result['student']->load('representative.user'));
    }
}
