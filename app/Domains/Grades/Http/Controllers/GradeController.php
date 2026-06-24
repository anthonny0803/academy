<?php

namespace App\Domains\Grades\Http\Controllers;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Grades\Http\Requests\Grades\StoreGradeRequest;
use App\Domains\Grades\Http\Requests\Grades\UpdateGradeRequest;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Repositories\GradeRepository;
use App\Domains\Grades\Services\Grades\DeleteGradeService;
use App\Domains\Grades\Services\Grades\StoreGradeService;
use App\Domains\Grades\Services\Grades\UpdateGradeService;
use App\Domains\Identity\Models\User;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradeController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;

    public function __construct(
        private EnrollmentRepository $enrollmentRepository,
        private GradeRepository $gradeRepository
    ) {}

    protected function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * Dashboard de asignaciones para el profesor logueado
     */
    public function teacherAssignments(): View|RedirectResponse
    {
        $user = $this->currentUser();

        if (! $user->isTeacher() || ! $user->teacher) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un perfil de profesor asignado.');
        }

        $teacher = $user->teacher;

        $assignments = SectionSubjectTeacher::where('teacher_id', $teacher->id)
            ->where('status', SectionSubjectTeacherStatus::Active->value)
            ->with([
                'section.academicPeriod',
                'subject',
                'gradeColumns',
            ])
            ->get()
            ->groupBy(fn ($sst) => $sst->section->academicPeriod->name);

        return view('grades.teacher-assignments', compact('assignments', 'teacher'));
    }

    /**
     * Vista principal: Tabla de calificaciones por asignación
     * Muestra estudiantes (filas) x evaluaciones (columnas)
     */
    public function index(SectionSubjectTeacher $sectionSubjectTeacher): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewForAssignment', [Grade::class, $sectionSubjectTeacher], function () use ($sectionSubjectTeacher) {
            $sectionSubjectTeacher->load([
                'section.academicPeriod',
                'section.enrollments' => fn ($q) => $q->active()->with('student.user'),
                'subject',
                'teacher.user',
                'gradeColumns' => fn ($q) => $q->orderBy('display_order'),
            ]);

            // Verificar que la configuración esté completa
            $isConfigurationComplete = $sectionSubjectTeacher->isConfigurationComplete();

            // Variables para la vista
            $gradeColumns = $sectionSubjectTeacher->gradeColumns;
            $enrollments = $sectionSubjectTeacher->section->enrollments;
            $academicPeriod = $sectionSubjectTeacher->section->academicPeriod;

            // Rango de notas desde el período académico (con defaults)
            $minGrade = $academicPeriod->min_grade ?? 0;
            $maxGrade = $academicPeriod->max_grade ?? 100;
            $passingGrade = $academicPeriod->passing_grade ?? 60;

            // Obtener todas las notas agrupadas por enrollment y luego por column
            $grades = $this->gradeRepository->forGradeColumns($gradeColumns->pluck('id')->all());

            // Agrupar: enrollment_id => [column_id => grade]
            $gradesByEnrollment = [];
            foreach ($grades as $grade) {
                $gradesByEnrollment[$grade->enrollment_id][$grade->grade_column_id] = $grade;
            }

            return view('grades.index', compact(
                'sectionSubjectTeacher',
                'gradeColumns',
                'enrollments',
                'gradesByEnrollment',
                'isConfigurationComplete',
                'academicPeriod',
                'minGrade',
                'maxGrade',
                'passingGrade'
            ));
        });
    }

    /**
     * Almacenar nota individual
     */
    public function store(
        StoreGradeRequest $request,
        StoreGradeService $storeService,
        GradeColumn $gradeColumn
    ): RedirectResponse|JsonResponse {
        $enrollment = $this->enrollmentRepository->findOrFail($request->validated()['enrollment_id']);

        return $this->authorizeOrRedirect('createForColumn', [Grade::class, $gradeColumn, $enrollment], function () use ($request, $storeService, $gradeColumn, $enrollment) {
            try {
                $grade = $storeService->handle($gradeColumn, $enrollment, $request->validated());

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => '¡Nota registrada correctamente!',
                        'grade' => $grade,
                    ]);
                }

                return redirect()
                    ->back()
                    ->with('success', '¡Nota registrada correctamente!');
            } catch (\Exception $e) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        });
    }

    /**
     * Actualizar nota individual
     */
    public function update(
        UpdateGradeRequest $request,
        UpdateGradeService $updateService,
        Grade $grade
    ): RedirectResponse|JsonResponse {
        return $this->authorizeOrRedirect('update', $grade, function () use ($request, $updateService, $grade) {
            try {
                $grade = $updateService->handle($grade, $request->validated());

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => '¡Nota actualizada correctamente!',
                        'grade' => $grade,
                    ]);
                }

                return redirect()
                    ->back()
                    ->with('success', '¡Nota actualizada correctamente!');
            } catch (\Exception $e) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        });
    }

    /**
     * Eliminar nota (solo Developer)
     */
    public function destroy(
        Grade $grade,
        DeleteGradeService $deleteService
    ): RedirectResponse|JsonResponse {
        return $this->authorizeOrRedirect('delete', $grade, function () use ($grade, $deleteService) {
            try {
                $deleteService->handle($grade);

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => '¡Nota eliminada correctamente!',
                    ]);
                }

                return redirect()
                    ->back()
                    ->with('success', '¡Nota eliminada correctamente!');
            } catch (\Exception $e) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->with('error', $e->getMessage());
            }
        });
    }
}
