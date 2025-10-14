<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\AcademicPeriod;
use App\Models\Section;
use App\Enums\EnrollmentStatus;
use App\Http\Requests\Enrollments\StoreEnrollmentRequest;
use App\Http\Requests\Enrollments\UpdateEnrollmentRequest;
use App\Http\Requests\Enrollments\TransferEnrollmentRequest;
use App\Http\Requests\Enrollments\PromoteEnrollmentRequest;
use App\Services\Enrollments\StoreEnrollmentService;
use App\Services\Enrollments\UpdateEnrollmentService;
use App\Services\Enrollments\TransferEnrollmentService;
use App\Services\Enrollments\PromoteEnrollmentService;
use App\Services\Enrollments\DeleteEnrollmentService;
use App\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', Enrollment::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $sectionId = $request->input('section_id');

            $sections = Section::active()->orderBy('name')->get();
            $statuses = EnrollmentStatus::toArray();

            $enrollments = Enrollment::query()
                ->with(['student.user', 'section.academicPeriod'])
                ->when($search !== '', fn($q) => $q->search($search))
                ->when($status && $status !== 'Todos', fn($q) => $q->byStatus($status))
                ->when($sectionId && $sectionId !== 'Todos', fn($q) => $q->forSection($sectionId))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();

            return view('enrollments.index', compact('enrollments', 'sections', 'statuses'));
        });
    }

    public function show(Enrollment $enrollment): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $enrollment, function () use ($enrollment) {
            $enrollment->load(['student.user', 'student.representative.user', 'section.academicPeriod', 'grades']);
            return view('enrollments.show', compact('enrollment'));
        });
    }

    public function create(Student $student): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Enrollment::class, function () use ($student) {
            $academicPeriods = AcademicPeriod::active()
                ->with(['sections' => fn($q) => $q->active()->orderBy('name')])
                ->orderBy('start_date', 'desc')
                ->get();

            return view('enrollments.create', compact('student', 'academicPeriods'));
        });
    }

    public function store(
        StoreEnrollmentRequest $request,
        StoreEnrollmentService $storeService,
        Student $student
    ): RedirectResponse {
        return $this->authorizeOrRedirect('create', Enrollment::class, function () use ($request, $storeService, $student) {
            $storeService->handle($student, $request->validated());

            return redirect()->route('students.show', $student)
                ->with('success', '¡Estudiante inscrito correctamente!');
        });
    }

    public function edit(Enrollment $enrollment): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $enrollment, function () use ($enrollment) {
            $statuses = EnrollmentStatus::toArray();
            return view('enrollments.edit', compact('enrollment', 'statuses'));
        });
    }

    public function update(
        UpdateEnrollmentRequest $request,
        UpdateEnrollmentService $updateService,
        Enrollment $enrollment
    ): RedirectResponse {
        return $this->authorizeOrRedirect('update', $enrollment, function () use ($request, $updateService, $enrollment) {
            $updateService->handle($enrollment, $request->validated());

            return redirect()->route('enrollments.show', $enrollment)
                ->with('success', '¡Estado de inscripción actualizado correctamente!');
        });
    }

    public function destroy(
        Enrollment $enrollment,
        DeleteEnrollmentService $deleteService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('delete', $enrollment, function () use ($enrollment, $deleteService) {
            $deleteService->handle($enrollment);

            return redirect()->route('students.show', $enrollment->student)
                ->with('success', '¡Inscripción eliminada correctamente!');
        });
    }

    public function transfer(
        TransferEnrollmentRequest $request,
        TransferEnrollmentService $transferService,
        Enrollment $enrollment
    ): RedirectResponse {
        return $this->authorizeOrRedirect('transfer', $enrollment, function () use ($request, $transferService, $enrollment) {
            $validated = $request->validated();

            $transferService->handle(
                $enrollment,
                $validated['section_id'],
                $validated['reason']
            );

            return redirect()->route('students.show', $enrollment->student)
                ->with('success', '¡Estudiante transferido a nueva sección correctamente!');
        });
    }

    public function promote(
        PromoteEnrollmentRequest $request,
        PromoteEnrollmentService $promoteService,
        Enrollment $enrollment
    ): RedirectResponse {
        return $this->authorizeOrRedirect('promote', $enrollment, function () use ($request, $promoteService, $enrollment) {
            $validated = $request->validated();

            $promoteService->handle(
                $enrollment,
                $validated['section_id']
            );

            return redirect()->route('students.show', $enrollment->student)
                ->with('success', '¡Estudiante promovido correctamente!');
        });
    }
}
