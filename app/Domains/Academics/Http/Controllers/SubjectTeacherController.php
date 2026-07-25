<?php

namespace App\Domains\Academics\Http\Controllers;

use App\Domains\Academics\Http\Requests\SubjectTeacher\StoreSubjectTeacherRequest;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\SubjectTeacher;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Repositories\SubjectRepository;
use App\Domains\Academics\Services\SubjectTeacher\DeleteSubjectTeacherService;
use App\Domains\Academics\Services\SubjectTeacher\StoreSubjectTeacherService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectTeacherController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;

    public function __construct(
        private SubjectRepository $subjectRepository
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', SubjectTeacher::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $subjectId = $request->input('subject_id');

            $allSubjects = $this->subjectRepository->activeOrdered();

            $subjects = $this->subjectRepository
                ->paginateWithActiveTeachers($search, $subjectId, 6)
                ->withQueryString();

            return view('subject-teacher.index', compact('subjects', 'allSubjects'));
        });
    }

    public function assign(Teacher $teacher): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', SubjectTeacher::class, function () use ($teacher) {
            $teacher->load('subjects');
            $subjects = $this->subjectRepository->activeOrdered();

            return view('subject-teacher.assign', compact('teacher', 'subjects'));
        });
    }

    public function store(
        StoreSubjectTeacherRequest $request,
        StoreSubjectTeacherService $storeService,
        Teacher $teacher
    ): RedirectResponse {
        return $this->authorizeOrRedirect('create', SubjectTeacher::class, function () use ($request, $storeService, $teacher) {
            $storeService->handle($teacher, $request->validated());

            return redirect()->route('teachers.show', $teacher)
                ->with('success', '¡Materias asignadas correctamente!');
        });
    }

    public function destroy(
        Teacher $teacher,
        Subject $subject,
        DeleteSubjectTeacherService $deleteService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('delete', SubjectTeacher::class, function () use ($teacher, $subject, $deleteService) {
            $deleteService->handle($teacher, $subject);

            return redirect()->route('teachers.show', $teacher)
                ->with('success', '¡Materia removida correctamente!');
        });
    }
}
