<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Representative;
use App\Services\StoreRepresentativeService;
use App\Services\UpdateRepresentativeService;
use App\Http\Requests\StoreRepresentativeRequest;
use App\Http\Requests\UpdateRepresentativeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Traits\AuthorizesRedirect;

class RepresentativeController extends Controller
{
    use AuthorizesRequests, AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', Representative::class, function () use ($request) {
            $search = trim($request->input('search', ''));
            $status = $request->input('status');

            // Only display if exists a search value.
            $query = Representative::searchWithFilters($search, $status);
            $representatives = $query ? $query->paginate(5) : collect();

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
            return view('representatives.create');
        });
    }

    public function store(StoreRepresentativeRequest $request, StoreRepresentativeService $storeService): RedirectResponse
    {
        try {
            $representative = $storeService->handle($request->validated());

            return redirect()->route('students.create', ['representative' => $representative->id])
                ->with('success', '¡Representante registrado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Representative $representative): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('edit', $representative, function () use ($representative) {
            return view('representatives.edit', compact('representative'));
        });
    }

    public function update(UpdateRepresentativeRequest $request, UpdateRepresentativeService $updateService, Representative $representative): RedirectResponse
    {
        try {
            $result = $updateService->handle($representative, $request->validated());

            $route = redirect()->route('representatives.show', $result['representative']);

            return $result['warning']
                ? $route->with('warning', '¡Representante actualizado! Se han ignorado algunos campos sensibles de empleados.')
                : $route->with('success', '¡Representante actualizado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function toggleActivation(Representative $representative): RedirectResponse
    {
        return $this->authorizeOrRedirect('toggle', $representative, function () use ($representative) {
            $representative->activation(!$representative->is_active);
            $status = $representative->is_active ? 'activado' : 'desactivado';

            return redirect()->route('representatives.show', $representative)
                ->with('success', "¡Representante {$status} correctamente!");
        });
    }
}
