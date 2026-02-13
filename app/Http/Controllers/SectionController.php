<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\AcademicPeriod;
use App\Models\Subject;
use App\Http\Requests\Sections\StoreSectionRequest;
use App\Http\Requests\Sections\UpdateSectionRequest;
use App\Services\Sections\StoreSectionService;
use App\Services\Sections\UpdateSectionService;
use App\Services\Sections\DeleteSectionService;
use App\Traits\AuthorizesRedirect;
use App\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class SectionController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;
    use CanToggleActivation;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', Section::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $academicPeriodId = $request->input('academic_period_id');

            $academicPeriods = AcademicPeriod::active()
                ->orderBy('start_date', 'desc')
                ->get();

            $sections = Section::query()
                ->with('academicPeriod')
                ->withCount(['enrollments' => fn($q) => $q->active()])
                ->when($search !== '', fn($q) => $q->search($search))
                ->when($status && $status !== 'Todos', function ($q) use ($status) {
                    $status === 'Activo' ? $q->active() : $q->inactive();
                })
                ->when($academicPeriodId && $academicPeriodId !== 'Todos', fn($q) => $q->forAcademicPeriod($academicPeriodId))
                ->orderBy('name')
                ->paginate(5)
                ->withQueryString();

            return view('sections.index', compact('sections', 'academicPeriods'));
        });
    }

    public function show(Section $section): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $section, function () use ($section) {
            $section->load([
                'academicPeriod',
                'enrollments' => fn($q) => $q->active()->with('student.user'),
                'sectionSubjectTeachers' => fn($q) => $q->with(['subject', 'teacher.user']),
            ]);

            $enrolledCount = $section->enrollments->count();

            return view('sections.show', compact('section', 'enrolledCount'));
        });
    }

    public function assignments(Section $section): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $section, function () use ($section) {
            // Cargar relaciones necesarias
            $section->load([
                'academicPeriod',
                'sectionSubjectTeachers.subject',
                'sectionSubjectTeachers.teacher.user'
            ]);

            // Materias activas para el select del modal
            $subjects = Subject::active()->orderBy('name')->get();

            // Construir array de profesores agrupados por materia
            $teachersBySubject = [];

            foreach ($subjects as $subject) {
                // Obtener profesores que PUEDEN impartir esta materia (desde subject_teacher)
                $teachers = $subject->teachers()
                    ->where('is_active', true)
                    ->with('user')
                    ->get();

                $teachersBySubject[$subject->id] = $teachers->map(function ($teacher) {
                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->user->full_name
                    ];
                })->toArray();
            }

            return view('sections.assignments', compact('section', 'subjects', 'teachersBySubject'));
        });
    }

    public function store(StoreSectionRequest $request, StoreSectionService $storeService): RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Section::class, function () use ($request, $storeService) {
            $storeService->handle($request->validated());

            return redirect()->route('sections.index')
                ->with('success', '¡Sección registrada correctamente!');
        });
    }

    public function update(UpdateSectionRequest $request, UpdateSectionService $updateService, Section $section): RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $section, function () use ($request, $updateService, $section) {
            $updateService->handle($section, $request->validated());

            return redirect()->route('sections.index')
                ->with('success', '¡Sección actualizada correctamente!');
        });
    }

    public function destroy(Section $section, DeleteSectionService $deleteService): RedirectResponse
    {
        return $this->authorizeOrRedirect('delete', $section, function () use ($section, $deleteService) {
            $deleteService->handle($section);

            return redirect()->route('sections.index')
                ->with('success', '¡Sección eliminada correctamente!');
        });
    }

    public function toggleActivation(Section $section): RedirectResponse
    {
        return $this->executeToggle($section);
    }
}