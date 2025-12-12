<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\AcademicPeriod;
use App\Models\Section;
use App\Enums\EnrollmentStatus;
use App\Http\Requests\Enrollments\StoreEnrollmentRequest;
use App\Http\Requests\Enrollments\TransferEnrollmentRequest;
use App\Http\Requests\Enrollments\PromoteEnrollmentRequest;
use App\Services\Enrollments\StoreEnrollmentService;
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
            $academicPeriodId = $request->input('academic_period_id');
            $sectionId = $request->input('section_id');
            
            $academicPeriods = AcademicPeriod::active()
                ->with(['sections' => fn($q) => $q->active()->orderBy('name')])
                ->orderBy('start_date', 'desc')
                ->get();

            $statuses = EnrollmentStatus::toArray();

            $enrollments = Enrollment::query()
                ->with(['student.user', 'section.academicPeriod'])
                ->when($search !== '', fn($q) => $q->search($search))
                ->when($status && $status !== 'Todos', fn($q) => $q->byStatus($status))
                ->when($academicPeriodId, function ($q) use ($academicPeriodId) {
                    $q->whereHas('section', fn($query) => $query->where('academic_period_id', $academicPeriodId));
                })
                ->when($sectionId && $sectionId !== 'Todos', fn($q) => $q->forSection($sectionId))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();

            return view('enrollments.index', compact('enrollments', 'academicPeriods', 'statuses'));
        });
    }

    public function show(Enrollment $enrollment): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $enrollment, function () use ($enrollment) {
            $enrollment->load(['student.user', 'student.representative.user', 'section.academicPeriod', 'grades.gradeColumn.sectionSubjectTeacher.subject']);
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

    public function destroy(
        Enrollment $enrollment,
        DeleteEnrollmentService $deleteService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('delete', $enrollment, function () use ($enrollment, $deleteService) {
            $student = $enrollment->student;
            $deleteService->handle($enrollment);

            return redirect()->route('students.show', $student)
                ->with('success', '¡Inscripción eliminada correctamente!');
        });
    }

    // =========================================
    // ACCIONES ESPECÍFICAS
    // =========================================

    /**
     * Mostrar formulario de TRANSFERENCIA
     * El estudiante se va a otra institución educativa.
     */
    public function showTransferForm(Enrollment $enrollment): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('transfer', $enrollment, function () use ($enrollment) {
            $enrollment->load(['student.user', 'section.academicPeriod']);

            return view('enrollments.transfer', compact('enrollment'));
        });
    }

    /**
     * Ejecutar transferencia
     */
    public function transfer(
        TransferEnrollmentRequest $request,
        TransferEnrollmentService $transferService,
        Enrollment $enrollment
    ): RedirectResponse {
        return $this->authorizeOrRedirect('transfer', $enrollment, function () use ($request, $transferService, $enrollment) {
            $transferService->handle($enrollment, $request->validated()['reason']);

            return redirect()->route('students.show', $enrollment->student)
                ->with('success', '¡Estudiante transferido correctamente! El estudiante ha salido del sistema.');
        });
    }

    /**
     * Mostrar formulario de PROMOCIÓN
     * El estudiante avanza de nivel dentro del MISMO período académico.
     */
    public function showPromoteForm(Enrollment $enrollment): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('promote', $enrollment, function () use ($enrollment) {
            $enrollment->load(['student.user', 'section.academicPeriod']);

            $academicPeriod = $enrollment->section->academicPeriod;

            // Verificar si el período permite promociones
            if (!$academicPeriod->isPromotable()) {
                return redirect()
                    ->route('enrollments.show', $enrollment)
                    ->with('error', "El período académico '{$academicPeriod->name}' no permite promociones.");
            }

            // Secciones del MISMO período (excluyendo la actual)
            $sections = Section::active()
                ->where('academic_period_id', $academicPeriod->id)
                ->where('id', '!=', $enrollment->section_id)
                ->orderBy('name')
                ->get();

            return view('enrollments.promote', compact('enrollment', 'sections', 'academicPeriod'));
        });
    }

    /**
     * Ejecutar promoción
     */
    public function promote(
        PromoteEnrollmentRequest $request,
        PromoteEnrollmentService $promoteService,
        Enrollment $enrollment
    ): RedirectResponse {
        return $this->authorizeOrRedirect('promote', $enrollment, function () use ($request, $promoteService, $enrollment) {
            $newEnrollment = $promoteService->handle($enrollment, $request->validated()['section_id']);

            return redirect()->route('students.show', $enrollment->student)
                ->with('success', "¡Estudiante promovido a {$newEnrollment->section->name} correctamente!");
        });
    }

    /**
     * Mostrar formulario de RETIRO
     * El estudiante abandona o es expulsado de la institución.
     */
    public function showWithdrawForm(Enrollment $enrollment): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('withdraw', $enrollment, function () use ($enrollment) {
            $enrollment->load(['student.user', 'section.academicPeriod']);

            return view('enrollments.withdraw', compact('enrollment'));
        });
    }
}