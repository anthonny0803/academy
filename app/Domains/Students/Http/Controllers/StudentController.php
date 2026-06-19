<?php

namespace App\Domains\Students\Http\Controllers;

use App\Domains\Academics\Repositories\AcademicPeriodRepository;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Representatives\Repositories\RepresentativeRepository;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use App\Domains\Shared\Traits\CanToggleActivation;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Http\Requests\ChangeSituationRequest;
use App\Domains\Students\Http\Requests\ConvertToSelfRepresentedRequest;
use App\Domains\Students\Http\Requests\ReassignRepresentativeRequest;
use App\Domains\Students\Http\Requests\StoreStudentRequest;
use App\Domains\Students\Http\Requests\UpdateStudentRequest;
use App\Domains\Students\Http\Requests\WithdrawStudentRequest;
use App\Domains\Students\Models\Student;
use App\Domains\Students\Repositories\StudentRepository;
use App\Domains\Students\Services\ChangeSituationService;
use App\Domains\Students\Services\ConvertToSelfRepresentedService;
use App\Domains\Students\Services\ReassignRepresentativeService;
use App\Domains\Students\Services\StoreStudentService;
use App\Domains\Students\Services\UpdateStudentService;
use App\Domains\Students\Services\WithdrawStudentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;
    use CanToggleActivation;

    public function __construct(
        private RepresentativeRepository $representativeRepository,
        private StudentRepository $studentRepository,
        private AcademicPeriodRepository $academicPeriodRepository
    ) {}

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

            $academicPeriods = $this->academicPeriodRepository->activeWithActiveSections();

            // If no search term is provided, return an empty collection
            if (empty($search)) {
                $students = collect();
            } else {
                $isActive = match ($status) {
                    'Activo' => true,
                    'Inactivo' => false,
                    default => null,
                };

                $students = $this->studentRepository
                    ->paginateForListing($search, $isActive, $academicPeriodId, $sectionId, 6)
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

            $academicPeriods = $this->academicPeriodRepository->activeWithActiveSections();

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
            $canEditSensitiveFields = ! $student->user->isEmployee();

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

    public function showReassignForm(Request $request, Student $student): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $student, function () use ($request, $student) {
            $student->load(['user', 'representative.user']);

            $search = trim((string) $request->input('search', ''));
            $representatives = collect();

            if (! empty($search)) {
                $representatives = $this->representativeRepository
                    ->paginateForSearch($search, 5)
                    ->withQueryString();
            }

            $canReassign = $this->currentUser()->isDeveloper() || $this->currentUser()->isSupervisor();
            $isSelfRepresented = $student->user_id === $student->representative?->user_id;

            return view('students.reassign-representative', compact('student', 'representatives', 'canReassign', 'isSelfRepresented'));
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
                $validated['relationship_type'],
                $validated['reason']
            );

            return redirect()->route('students.show', $student)
                ->with('success', '¡Representante reasignado correctamente!');
        });
    }

    public function showConvertToSelfRepresentedForm(Student $student): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('convertToSelfRepresented', $student, function () use ($student) {
            $student->load(['user', 'representative.user']);

            $canConvert = $this->currentUser()->isDeveloper() || $this->currentUser()->isSupervisor();
            $isSelfRepresented = $student->user_id === $student->representative?->user_id;
            $isOfAge = $student->age !== null && $student->age >= 18;

            return view('students.convert-to-self-represented', compact('student', 'canConvert', 'isSelfRepresented', 'isOfAge'));
        });
    }

    public function convertToSelfRepresented(
        ConvertToSelfRepresentedRequest $request,
        ConvertToSelfRepresentedService $convertService,
        Student $student
    ): RedirectResponse {
        return $this->authorizeOrRedirect('convertToSelfRepresented', $student, function () use ($request, $convertService, $student) {
            $convertService->handle($student, $request->validated()['reason'] ?? null);

            return redirect()->route('students.show', $student)
                ->with('success', '¡El estudiante ahora es auto-representante!');
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
