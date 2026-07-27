<?php

namespace App\Domains\Enrollments\Http\Controllers;

use App\Domains\Academics\Repositories\AcademicPeriodRepository;
use App\Domains\Academics\Repositories\SectionRepository;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Http\Requests\PromoteEnrollmentRequest;
use App\Domains\Enrollments\Http\Requests\StoreEnrollmentRequest;
use App\Domains\Enrollments\Http\Requests\TransferEnrollmentRequest;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Enrollments\Services\DeleteEnrollmentService;
use App\Domains\Enrollments\Services\EnrollmentDetailsService;
use App\Domains\Enrollments\Services\PromoteEnrollmentService;
use App\Domains\Enrollments\Services\StoreEnrollmentService;
use App\Domains\Enrollments\Services\TransferEnrollmentService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use App\Domains\Students\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;

    public function __construct(
        private EnrollmentRepository $enrollmentRepository,
        private SectionRepository $sectionRepository,
        private AcademicPeriodRepository $academicPeriodRepository
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', Enrollment::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $academicPeriodId = $request->input('academic_period_id');
            $sectionId = $request->input('section_id');

            $academicPeriods = $this->academicPeriodRepository->activeWithActiveSections();

            $statuses = EnrollmentStatus::toArray();

            $statusFilter = $status && $status !== 'Todos' ? $status : null;
            $sectionFilter = $sectionId && $sectionId !== 'Todos' ? $sectionId : null;

            $enrollments = $this->enrollmentRepository
                ->paginateForListing($search, $statusFilter, $academicPeriodId, $sectionFilter, 6)
                ->withQueryString();

            return view('enrollments.index', compact('enrollments', 'academicPeriods', 'statuses'));
        });
    }

    public function show(Enrollment $enrollment, EnrollmentDetailsService $detailsService): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $enrollment, function () use ($enrollment, $detailsService) {
            return view('enrollments.show', $detailsService->handle($enrollment));
        });
    }

    public function create(Student $student): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Enrollment::class, function () use ($student) {
            $academicPeriods = $this->academicPeriodRepository->activeWithActiveSections();

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
            if (! $academicPeriod->isPromotable()) {
                return redirect()
                    ->route('enrollments.show', $enrollment)
                    ->with('error', "El período académico '{$academicPeriod->name}' no permite promociones.");
            }

            // Secciones del MISMO período (excluyendo la actual)
            $sections = $this->sectionRepository->activeForPeriodExcept($academicPeriod->id, $enrollment->section_id);

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
