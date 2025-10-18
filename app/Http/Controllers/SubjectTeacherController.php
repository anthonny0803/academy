<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Http\Requests\SubjectTeacher\StoreSubjectTeacherRequest;
use App\Services\SubjectTeacher\StoreSubjectTeacherService;
use App\Services\SubjectTeacher\DeleteSubjectTeacherService;
use App\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubjectTeacherController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', SubjectTeacher::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $subjectId = $request->input('subject_id');

            $allSubjects = Subject::active()->orderBy('name')->get();

            $subjects = Subject::query()
                ->with(['teachers' => function ($q) {
                    $q->where('is_active', true)
                        ->with('user');
                }])
                ->active()
                ->when($search !== '', fn($q) => $q->search($search))
                ->when($subjectId, fn($q) => $q->where('id', $subjectId))
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();

            return view('subject-teacher.index', compact('subjects', 'allSubjects'));
        });
    }

    public function assign(Teacher $teacher): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', SubjectTeacher::class, function () use ($teacher) {
            $teacher->load('subjects');
            $subjects = Subject::active()->orderBy('name')->get();

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
