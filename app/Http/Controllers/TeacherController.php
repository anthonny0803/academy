<?php

namespace App\Http\Controllers;

use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Requests\Teachers\StoreTeacherRequest;
use App\Domains\Academics\Requests\Teachers\UpdateTeacherRequest;
use App\Domains\Academics\Services\Teachers\StoreTeacherService;
use App\Domains\Academics\Services\Teachers\UpdateTeacherService;
use App\Domains\Identity\Models\User;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use App\Domains\Shared\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;
    use CanToggleActivation;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', Teacher::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');

            // Security: Only display if exists a search value.
            if (empty($search)) {
                $teachers = collect();
            } else {
                $teachers = Teacher::query()
                    ->search($search)
                    ->when($status && $status !== 'Todos', function ($q) use ($status) {
                        $status === 'Activo' ? $q->active() : $q->inactive();
                    })
                    ->orderByUserName()
                    ->with('user')
                    ->paginate(6)
                    ->withQueryString();
            }

            return view('teachers.index', compact('teachers'));
        });
    }

    public function show(Teacher $teacher): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $teacher, function () use ($teacher) {
            return view('teachers.show', compact('teacher'));
        });
    }

    public function create(): View|RedirectResponse
    {
        $sexes = Sex::toArray();

        return $this->authorizeOrRedirect('create', Teacher::class, function () use ($sexes) {
            return view('teachers.create', compact('sexes'));
        });
    }

    public function store(StoreTeacherRequest $request, StoreTeacherService $storeService): RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Teacher::class, function () use ($request, $storeService) {
            $teacher = $storeService->handle($request->validated());

            return redirect()->route('teachers.show', $teacher)
                ->with('success', '¡Profesor registrado correctamente!');
        });
    }

    public function edit(Teacher $teacher): View|RedirectResponse
    {
        $sexes = Sex::toArray();

        return $this->authorizeOrRedirect('update', $teacher, function () use ($teacher, $sexes) {
            return view('teachers.edit', compact('teacher', 'sexes'));
        });
    }

    public function update(UpdateTeacherRequest $request, UpdateTeacherService $updateService, Teacher $teacher): RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $teacher, function () use ($request, $updateService, $teacher) {
            $teacher = $updateService->handle($teacher, $request->validated());

            return redirect()
                ->route('teachers.show', $teacher)
                ->with('success', '¡Profesor actualizado correctamente!');
        });
    }

    public function toggleActivation(Teacher $teacher): RedirectResponse
    {
        return $this->executeToggle($teacher);
    }
}
