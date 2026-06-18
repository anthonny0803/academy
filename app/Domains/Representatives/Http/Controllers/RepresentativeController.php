<?php

namespace App\Domains\Representatives\Http\Controllers;

use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Http\Requests\StoreRepresentativeRequest;
use App\Domains\Representatives\Http\Requests\UpdateRepresentativeRequest;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Representatives\Repositories\RepresentativeRepository;
use App\Domains\Representatives\Services\StoreRepresentativeService;
use App\Domains\Representatives\Services\UpdateRepresentativeService;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use App\Domains\Shared\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RepresentativeController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;
    use CanToggleActivation;

    public function __construct(
        private RepresentativeRepository $representativeRepository
    ) {}

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

            // Security: Only display if exists a search value.
            if (empty($search)) {
                $representatives = collect();
            } else {
                $isActive = match ($status) {
                    'Activo' => true,
                    'Inactivo' => false,
                    default => null,
                };
                $hasStudents = match ($studentsFilter) {
                    'con' => true,
                    'sin' => false,
                    default => null,
                };

                $representatives = $this->representativeRepository
                    ->paginateForListing($search, $isActive, $hasStudents, 6)
                    ->withQueryString();
            }

            return view('representatives.index', compact('representatives'));
        });
    }

    public function show(Representative $representative): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $representative, function () use ($representative) {
            $representative->load(['user.roles', 'students.user']);

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
            $canEditSensitiveFields = ! $representative->user->isEmployee();

            return view('representatives.edit', compact('representative', 'sexes', 'canEditSensitiveFields'));
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
}
