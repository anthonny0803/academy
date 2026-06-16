<?php

namespace App\Http\Controllers;

use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Requests\Subjects\StoreSubjectRequest;
use App\Domains\Academics\Requests\Subjects\UpdateSubjectRequest;
use App\Domains\Academics\Services\Subjects\DeleteSubjectService;
use App\Domains\Academics\Services\Subjects\StoreSubjectService;
use App\Domains\Academics\Services\Subjects\UpdateSubjectService;
use App\Domains\Identity\Models\User;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use App\Domains\Shared\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubjectController extends Controller
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
        return $this->authorizeOrRedirect('viewAny', Subject::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');

            $subjects = Subject::query()
                ->when($search !== '', fn ($q) => $q->search($search))
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
