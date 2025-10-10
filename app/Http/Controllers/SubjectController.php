<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use App\Http\Requests\Subjects\StoreSubjectRequest;
use App\Http\Requests\Subjects\UpdateSubjectRequest;
use App\Services\Subjects\StoreSubjectService;
use App\Services\Subjects\UpdateSubjectService;
use App\Services\Subjects\DeleteSubjectService;
use App\Traits\AuthorizesRedirect;
use App\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class SubjectController extends Controller
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
        return $this->authorizeOrRedirect('viewAny', Subject::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');

            $subjects = Subject::query()
                ->when($search !== '', fn($q) => $q->search($search))
                ->when($status && $status !== 'Todos', function ($q) use ($status) {
                    $status === 'Activo' ? $q->active() : $q->inactive();
                })
                ->orderBy('name')
                ->paginate(6)
                ->withQueryString();

            return view('subjects.index', compact('subjects'));
        });
    }

    public function store(StoreSubjectRequest $request, StoreSubjectService $storeService): RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Subject::class, function () use ($request, $storeService) {
            $storeService->handle($request->validated());

            return redirect()->route('subjects.index')
                ->with('success', '¡Asignatura registrada correctamente!');
        });
    }

    public function update(UpdateSubjectRequest $request, UpdateSubjectService $updateService, Subject $subject): RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $subject, function () use ($request, $updateService, $subject) {
            $updateService->handle($subject, $request->validated());

            return redirect()->route('subjects.index')
                ->with('success', '¡Asignatura actualizada correctamente!');
        });
    }

    public function destroy(Subject $subject, DeleteSubjectService $deleteService): RedirectResponse
    {
        return $this->authorizeOrRedirect('delete', $subject, function () use ($subject, $deleteService) {
            $deleteService->handle($subject);

            return redirect()->route('subjects.index')
                ->with('success', '¡Asignatura eliminada correctamente!');
        });
    }

    public function toggleActivation(Subject $subject): RedirectResponse
    {
        return $this->executeToggle($subject);
    }
}
