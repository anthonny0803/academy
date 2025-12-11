<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Representative;
use App\Models\AcademicPeriod;
use App\Models\Section;
use App\Enums\Sex;
use App\Enums\StudentSituation;
use App\Enums\RelationshipType;
use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Http\Requests\Students\ReassignRepresentativeRequest;
use App\Http\Requests\Students\ChangeSituationRequest;
use App\Http\Requests\Students\WithdrawStudentRequest;
use App\Services\Students\StoreStudentService;
use App\Services\Students\UpdateStudentService;
use App\Services\Students\ReassignRepresentativeService;
use App\Services\Students\ChangeSituationService;
use App\Services\Students\WithdrawStudentService;
use App\Traits\AuthorizesRedirect;
use App\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
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
        return $this->authorizeOrRedirect('viewAny', Student::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $academicPeriodId = $request->input('academic_period_id');
            $sectionId = $request->input('section_id');

            $academicPeriods = AcademicPeriod::active()
                ->with(['sections' => fn($q) => $q->active()->orderBy('name')])
                ->orderBy('start_date', 'desc')
                ->get();

            // If no search term is provided, return an empty collection
            if (empty($search)) {
                $students = collect();
            } else {
                $students = Student::query()
                    ->with(['user', 'representative.user', 'enrollments.section'])
                    ->search($search)
                    ->when($status && $status !== 'Todos', function ($q) use ($status) {
                        $status === 'Activo' ? $q->active() : $q->inactive();
                    })
                    ->when($academicPeriodId, function ($q) use ($academicPeriodId) {
                        $q->whereHas('enrollments.section', fn($query) => $query->where('academic_period_id', $academicPeriodId));
                    })
                    ->when($sectionId, function ($q) use ($sectionId) {
                        $q->whereHas('enrollments', fn($query) => $query->where('section_id', $sectionId));
                    })
                    ->join('users', 'students.user_id', '=', 'users.id')
                    ->orderBy('users.name')
                    ->orderBy('users.last_name')
                    ->select('students.*')
                    ->paginate(10)
                    ->withQueryString();
            }

            return view('students.index', compact('students', 'academicPeriods'));
        });
    }

    public function show(Student $student): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $student, function () use ($student) {
            $student->load(['user', 'representative.user', 'enrollments.section.academicPeriod']);
            return view('students.show', compact('student'));
        });
    }

    public function create(Representative $representative): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Student::class, function () use ($representative) {
            $sexes = Sex::toArray();
            $relationshipTypes = RelationshipType::toArray();

            $academicPeriods = AcademicPeriod::active()
                ->with(['sections' => fn($q) => $q->active()->orderBy('name')])
                ->orderBy('start_date', 'desc')
                ->get();

            return view('students.create', compact('representative', 'sexes', 'relationshipTypes', 'academicPeriods'));
        });
    }

    public function store(
        StoreStudentRequest $request,
        StoreStudentService $storeService,
        Representative $representative
    ): RedirectResponse {
        return $this->authorizeOrRedirect('create', Student::class, function () use ($request, $storeService, $representative) {
            $student = $storeService->handle($representative, $request->validated());

            return redirect()->route('students.show', $student)
                ->with('success', '¡Estudiante registrado correctamente!');
        });
    }

    public function edit(Student $student): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $student, function () use ($student) {
            $sexes = Sex::toArray();
            $relationshipTypes = RelationshipType::toArray();
            $canEditSensitiveFields = !$student->user->isEmployee();

            return view('students.edit', compact('student', 'sexes', 'relationshipTypes', 'canEditSensitiveFields'));
        });
    }

    public function update(
        UpdateStudentRequest $request,
        UpdateStudentService $updateService,
        Student $student
    ): RedirectResponse {
        return $this->authorizeOrRedirect('update', $student, function () use ($request, $updateService, $student) {
            $result = $updateService->handle($student, $request->validated());
            $route = redirect()->route('students.show', $result['student']);

            return $result['userFieldsIgnored']
                ? $route->with('warning', 'Estudiante actualizado. Los datos personales de usuarios con rol de empleado pueden cambiarse desde su perfil.')
                : $route->with('success', '¡Estudiante actualizado correctamente!');
        });
    }

    public function reassignRepresentative(
        ReassignRepresentativeRequest $request,
        ReassignRepresentativeService $reassignService,
        Student $student
    ): RedirectResponse {
        return $this->authorizeOrRedirect('reassignRepresentative', $student, function () use ($request, $reassignService, $student) {
            $validated = $request->validated();

            $reassignService->handle(
                $student,
                $validated['representative_id'],
                $validated['reason']
            );

            return redirect()->route('students.show', $student)
                ->with('success', '¡Representante reasignado correctamente!');
        });
    }

    public function changeSituation(
        ChangeSituationRequest $request,
        ChangeSituationService $changeSituationService,
        Student $student
    ): RedirectResponse {
        return $this->authorizeOrRedirect('changeSituation', $student, function () use ($request, $changeSituationService, $student) {
            $situation = StudentSituation::from($request->validated()['situation']);
            $changeSituationService->handle($student, $situation);

            return redirect()->back()
                ->with('success', '¡Situación del estudiante actualizada correctamente!');
        });
    }

    public function showWithdrawForm(Student $student): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('withdraw', $student, function () use ($student) {
            $student->load(['user', 'enrollments' => function ($q) {
                $q->active()->with('section.academicPeriod');
            }]);

            $activeEnrollments = $student->enrollments;

            return view('students.withdraw', compact('student', 'activeEnrollments'));
        });
    }

    public function withdraw(
        WithdrawStudentRequest $request,
        WithdrawStudentService $withdrawService,
        Student $student
    ): RedirectResponse {
        return $this->authorizeOrRedirect('withdraw', $student, function () use ($request, $withdrawService, $student) {
            $result = $withdrawService->handle($student, $request->validated()['reason']);

            return redirect()->route('students.show', $student)
                ->with('success', "¡Estudiante retirado correctamente! Se actualizaron {$result['enrollments_withdrawn']} inscripción(es).");
        });
    }
}
