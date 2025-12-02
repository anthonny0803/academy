<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grade;
use App\Models\GradeColumn;
use App\Models\SectionSubjectTeacher;
use App\Models\Enrollment;
use App\Http\Requests\Grades\StoreGradeRequest;
use App\Http\Requests\Grades\UpdateGradeRequest;
use App\Services\Grades\StoreGradeService;
use App\Services\Grades\UpdateGradeService;
use App\Services\Grades\DeleteGradeService;
use App\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradeController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;

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

        if (!$user->isTeacher() || !$user->teacher) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un perfil de profesor asignado.');
        }

        $teacher = $user->teacher;

        $assignments = SectionSubjectTeacher::where('teacher_id', $teacher->id)
            ->where('status', 'activo')
            ->with([
                'section.academicPeriod',
                'subject',
                'gradeColumns',
            ])
            ->get()
            ->groupBy(fn($sst) => $sst->section->academicPeriod->name);

        return view('grades.teacher-assignments', compact('assignments', 'teacher'));
    }

    /**
     * Vista principal: Tabla de calificaciones por asignación
     * Muestra estudiantes (filas) x evaluaciones (columnas)
     */
    public function index(SectionSubjectTeacher $sectionSubjectTeacher): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', Grade::class, function () use ($sectionSubjectTeacher) {
            $sectionSubjectTeacher->load([
                'section.academicPeriod',
                'section.enrollments' => fn($q) => $q->active()->with('student.user'),
                'subject',
                'teacher.user',
                'gradeColumns' => fn($q) => $q->orderBy('display_order'),
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
            $grades = Grade::whereIn('grade_column_id', $gradeColumns->pluck('id'))
                ->get();

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
        $enrollment = Enrollment::findOrFail($request->validated()['enrollment_id']);

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
     * Almacenar notas en lote (para entrada rápida)
     */
    public function storeBatch(
        Request $request,
        StoreGradeService $storeService,
        GradeColumn $gradeColumn
    ): JsonResponse {
        $this->authorize('create', Grade::class);

        $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.enrollment_id' => ['required', 'integer', 'exists:enrollments,id'],
            'grades.*.value' => ['required', 'numeric'],
            'grades.*.observation' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $results = $storeService->handleBatch($gradeColumn, $request->input('grades'));

            return response()->json([
                'success' => true,
                'message' => "Creadas: {$results['created']}, Actualizadas: {$results['updated']}",
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
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