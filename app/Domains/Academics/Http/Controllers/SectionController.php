<?php

namespace App\Domains\Academics\Http\Controllers;

use App\Domains\Academics\Http\Requests\Sections\StoreSectionRequest;
use App\Domains\Academics\Http\Requests\Sections\UpdateSectionRequest;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Repositories\SectionRepository;
use App\Domains\Academics\Services\Sections\DeleteSectionService;
use App\Domains\Academics\Services\Sections\StoreSectionService;
use App\Domains\Academics\Services\Sections\UpdateSectionService;
use App\Domains\Identity\Models\User;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use App\Domains\Shared\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SectionController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;
    use CanToggleActivation;

    public function __construct(
        private SectionRepository $sectionRepository
    ) {}

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

            $isActive = match ($status) {
                'Activo' => true,
                'Inactivo' => false,
                default => null,
            };
            $periodFilter = $academicPeriodId && $academicPeriodId !== 'Todos' ? $academicPeriodId : null;

            $sections = $this->sectionRepository
                ->paginateForListing($search, $isActive, $periodFilter, 6)
                ->withQueryString();

            return view('sections.index', compact('sections', 'academicPeriods'));
        });
    }

    public function show(Section $section): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $section, function () use ($section) {
            $section->load([
                'academicPeriod',
                'enrollments' => fn ($q) => $q->active()->with('student.user'),
                'sectionSubjectTeachers' => fn ($q) => $q->with(['subject', 'teacher.user']),
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
                'sectionSubjectTeachers.teacher.user',
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
                        'name' => $teacher->user->full_name,
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
