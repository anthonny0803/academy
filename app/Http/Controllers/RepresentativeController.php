<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Representative;
use App\Enums\Sex;
use App\Http\Requests\Representatives\StoreRepresentativeRequest;
use App\Http\Requests\Representatives\UpdateRepresentativeRequest;
use App\Services\Representatives\StoreRepresentativeService;
use App\Services\Representatives\UpdateRepresentativeService;
use App\Traits\AuthorizesRedirect;
use App\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RepresentativeController extends Controller
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
        return $this->authorizeOrRedirect('viewAny', Representative::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $studentsFilter = $request->input('students');

            if (empty($search)) {
                $representatives = collect();
            } else {
                $representatives = Representative::query()
                    ->join('users', 'representatives.user_id', '=', 'users.id')
                    ->select('representatives.*')
                    ->search($search)
                    ->when($status && $status !== 'Todos', function ($q) use ($status) {
                        $status === 'Activo' ? $q->active() : $q->inactive();
                    })
                    ->when($studentsFilter === 'con', fn($q) => $q->hasStudents())
                    ->when($studentsFilter === 'sin', fn($q) => $q->withoutStudents())
                    ->with(['user', 'students'])
                    ->orderBy('users.name')
                    ->orderBy('users.last_name')
                    ->paginate(5)
                    ->withQueryString();
            }

            return view('representatives.index', compact('representatives'));
        });
    }

    public function show(Representative $representative): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $representative, function () use ($representative) {
            return view('representatives.show', compact('representative'));
        });
    }

    public function create(): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Representative::class, function () {
            $sexes = Sex::toArray();
            return view('representatives.create', compact('sexes'));
        });
    }

    public function store(StoreRepresentativeRequest $request, StoreRepresentativeService $createService): RedirectResponse
    {
        return $this->authorizeOrRedirect('create', Representative::class, function () use ($request, $createService) {
            $representative = $createService->handle($request->validated());

            return redirect()
                ->route('representatives.students.create', ['representative' => $representative->id])
                ->with('success', '¡Representante registrado correctamente!');
        });
    }

    public function edit(Representative $representative): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $representative, function () use ($representative) {
            $sexes = Sex::toArray();
            $canEditEmail = !$representative->user->isEmployee();

            return view('representatives.edit', compact('representative', 'sexes', 'canEditEmail'));
        });
    }

    public function update(
        UpdateRepresentativeRequest $request,
        UpdateRepresentativeService $updateService,
        Representative $representative
    ): RedirectResponse {
        return $this->authorizeOrRedirect('update', $representative, function () use (
            $request,
            $updateService,
            $representative
        ) {
            $result = $updateService->handle($representative, $request->validated());
            $route = redirect()->route('representatives.show', $result['representative']);

            return $result['userFieldsIgnored']
                ? $route->with('warning', 'Representante actualizado. Los datos sensibles de usuarios con rol de empleado pueden cambiarse desde su perfil.')
                : $route->with('success', '¡Representante actualizado correctamente!');
        });
    }

    public function toggleActivation(Representative $representative): RedirectResponse
    {
        return $this->executeToggle($representative);
    }
}
