<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Enums\Sex;
use App\Http\Requests\Teachers\StoreTeacherRequest;
use App\Http\Requests\Teachers\UpdateTeacherRequest;
use App\Services\Teachers\StoreTeacherService;
use App\Services\Teachers\UpdateTeacherService;
use App\Traits\AuthorizesRedirect;
use App\Traits\CanToggleActivation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherController extends Controller
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
