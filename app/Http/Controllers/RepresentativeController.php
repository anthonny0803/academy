<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Representative;
use App\Http\Requests\StoreRepresentativeRequest;
use App\Http\Requests\UpdateRepresentativeRequest;
use App\Services\StoreRepresentativeService;
use App\Services\UpdateRepresentativeService;
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

            if (empty($search)) {
                $representatives = collect();
            } else {
                $representatives = Representative::query()
                    ->join('users', 'representatives.user_id', '=', 'users.id')
                    ->select('representatives.*') // Solo campos de representatives
                    ->search($search)
                    ->when($status && $status !== 'Todos', function ($q) use ($status) {
                        $status === 'Activo' ? $q->active() : $q->inactive();
                    })
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
            return view('representatives.create');
        });
    }

    public function store(StoreRepresentativeRequest $request, StoreRepresentativeService $storeService): RedirectResponse
    {
        $representative = $storeService->handle($request->validated());

        return redirect()->route('representatives.students.create', ['representative' => $representative->id])
            ->with('success', '¡Representante registrado correctamente!');
    }

    public function edit(Representative $representative): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('edit', $representative, function () use ($representative) {
            return view('representatives.edit', compact('representative'));
        });
    }

    public function update(UpdateRepresentativeRequest $request, UpdateRepresentativeService $updateService, Representative $representative): RedirectResponse
    {
        $result = $updateService->handle($representative, $request->validated());
        $route = redirect()->route('representatives.show', $result['representative']);

        return $result['warning']
            ? $route->with('warning', '¡Representante actualizado! Se han ignorado algunos campos sensibles de empleados.')
            : $route->with('success', '¡Representante actualizado correctamente!');
    }

    public function toggleActivation(Representative $representative): RedirectResponse
    {
        return $this->executeToggle($representative);
    }
}
