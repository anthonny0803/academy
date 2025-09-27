<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Services\StoreTeacherService;
use App\Services\UpdateTeacherService;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Traits\AuthorizesRedirect;

class TeacherController extends Controller
{
    use AuthorizesRequests, AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Teacher::class);

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        // Only display if exists a search value.
        $query = Teacher::searchWithFilters($search, $status);
        $teachers = $query ? $query->paginate(5) : collect();

        return view('teachers.index', compact('teachers'));
    }

    public function show(Teacher $teacher): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $teacher, function () use ($teacher) {
            return view('teachers.show', compact('teacher'));
        });
    }

    public function create(): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Teacher::class, function () {
            return view('teachers.create');
        });
    }

    public function store(StoreTeacherRequest $request, StoreTeacherService $storeService): RedirectResponse
    {
        try {
            $teacher = $storeService->handle($request->validated());
            return redirect()->route('teachers.show', $teacher)
                ->with('success', '¡Profesor registrado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Teacher $teacher): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $teacher, function () use ($teacher) {
            return view('teachers.edit', compact('teacher'));
        });
    }

    public function update(Teacher $teacher, UpdateTeacherRequest $request, UpdateTeacherService $updateService): RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $teacher, function () use ($teacher, $request, $updateService) {
            try {
                $updateService->handle($teacher, $request->validated());
                return redirect()->route('teachers.show', $teacher)
                    ->with('success', '¡Profesor actualizado correctamente!');
            } catch (\Exception $e) {
                return redirect()->back()->withInput()
                    ->with('error', $e->getMessage());
            }
        });
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        return $this->authorizeOrRedirect('delete', $teacher, function () use ($teacher) {
            $teacher->delete();
            return redirect()->route('teachers.index')
                ->with('success', '¡Profesor eliminado correctamente!');
        });
    }

    public function toggleActivation(Teacher $teacher): RedirectResponse
    {
        return $this->authorizeOrRedirect('toggle', $teacher, function () use ($teacher) {
            $teacher->activation(!$teacher->is_active);
            $status = $teacher->is_active ? 'activado' : 'desactivado';

            return redirect()->route('teachers.show', $teacher)
                ->with('success', "¡Profesor {$status} correctamente!");
        });
    }
}
