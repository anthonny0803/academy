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
            $enrollment->load([
                'student.user',
                'student.representative.user',
                'section.academicPeriod',
                'section.sectionSubjectTeachers' => fn($q) => $q->active()->with([
                    'subject',
                    'teacher.user',
                    'gradeColumns' => fn($q) => $q->orderBy('display_order'),
                ]),
                'grades.gradeColumn',
            ]);

            $academicPeriod = $enrollment->section->academicPeriod;
            $passingGrade = $academicPeriod->passing_grade ?? 60;

            $subjectsData = $enrollment->section->sectionSubjectTeachers->map(function ($sst) use ($enrollment) {
                $average = $sst->calculateStudentAverage($enrollment->id);
                
                $gradesDetail = $sst->gradeColumns->map(function ($column) use ($enrollment) {
                    $grade = $enrollment->grades->firstWhere('grade_column_id', $column->id);
                    return [
                        'column_name' => $column->name,
                        'weight' => $column->weight,
                        'value' => $grade?->value,
                        'observation' => $grade?->observation,
                    ];
                });

                return [
                    'sst_id' => $sst->id,
                    'subject_name' => $sst->subject->name,
                    'teacher_name' => $sst->teacher->user->full_name,
                    'average' => $average,
                    'grades_detail' => $gradesDetail,
                ];
            });

            return view('enrollments.show', compact('enrollment', 'subjectsData', 'passingGrade'));
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
                ->with('success', 'Â¡Estudiante inscrito correctamente!');
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
                ->with('success', 'Â¡InscripciÃ³n eliminada correctamente!');
        });
    }

    // =========================================
    // ACCIONES ESPECÃFICAS
    // =========================================

    /**
     * Mostrar formulario de TRANSFERENCIA
     * El estudiante se va a otra instituciÃ³n educativa.
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
                ->with('success', 'Â¡Estudiante transferido correctamente! El estudiante ha salido del sistema.');
        });
    }

    /**
     * Mostrar formulario de PROMOCIÃ“N
     * El estudiante avanza de nivel dentro del MISMO perÃ­odo acadÃ©mico.
     */
    public function showPromoteForm(Enrollment $enrollment): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('promote', $enrollment, function () use ($enrollment) {
            $enrollment->load(['student.user', 'section.academicPeriod']);

            $academicPeriod = $enrollment->section->academicPeriod;

            // Verificar si el perÃ­odo permite promociones
            if (!$academicPeriod->isPromotable()) {
                return redirect()
                    ->route('enrollments.show', $enrollment)
                    ->with('error', "El perÃ­odo acadÃ©mico '{$academicPeriod->name}' no permite promociones.");
            }

            // Secciones del MISMO perÃ­odo (excluyendo la actual)
            $sections = Section::active()
                ->where('academic_period_id', $academicPeriod->id)
                ->where('id', '!=', $enrollment->section_id)
                ->orderBy('name')
                ->get();

            return view('enrollments.promote', compact('enrollment', 'sections', 'academicPeriod'));
        });
    }

    /**
     * Ejecutar promociÃ³n
     */
    public function promote(
        PromoteEnrollmentRequest $request,
        PromoteEnrollmentService $promoteService,
        Enrollment $enrollment
    ): RedirectResponse {
        return $this->authorizeOrRedirect('promote', $enrollment, function () use ($request, $promoteService, $enrollment) {
            $newEnrollment = $promoteService->handle($enrollment, $request->validated()['section_id']);

            return redirect()->route('students.show', $enrollment->student)
                ->with('success', "Â¡Estudiante promovido a {$newEnrollment->section->name} correctamente!");
        });
    }

    /**
     * Mostrar formulario de RETIRO
     * El estudiante abandona o es expulsado de la instituciÃ³n.
     */
    public function showWithdrawForm(Enrollment $enrollment): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('withdraw', $enrollment, function () use ($enrollment) {
            $enrollment->load(['student.user', 'section.academicPeriod']);

            return view('enrollments.withdraw', compact('enrollment'));
        });
    }
}