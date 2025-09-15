<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Representative;
use App\Services\StoreRepresentativeService;
use App\Services\UpdateRepresentativeService;
use App\Services\RepresentativeActivationService;
use App\Http\Requests\StoreRepresentativeRequest;
use App\Http\Requests\UpdateRepresentativeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class RepresentativeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get the currently authenticated user.
     *
     * @return User
     */
    protected function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * Display a listing of the representatives.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $this->authorize('viewAny', Representative::class);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $representatives = collect(); // Initialize an empty collection for representatives
        $search = trim($request->input('search', '')); // Trim whitespace to prevent empty searches

        // If there's a search term, filter representatives accordingly
        if ($search !== '') {
            $query = Representative::with(['user.roles'])
                ->whereHas('user', function ($q) use ($search) {
                    $q->role('Representante')
                        ->where(function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });

            // Filter by representative status if provided
            if ($request->filled('status') && $request->input('status') !== 'Todos') {
                $isActive = $request->input('status') === 'Activo' ? 1 : 0;
                $query->where('representatives.is_active', $isActive);
            }

            // Join with users table to order by user name
            $query->join('users', 'representatives.user_id', '=', 'users.id')
                ->orderBy('users.name', 'asc')
                ->select('representatives.*');

            $representatives = $query->paginate(6); // Paginate results, 6 per page
        }

        return view('representatives.index', compact('representatives'));
    }

    /**
     * Display the specified representative.
     *
     * @param Representative $representative
     * @return View|RedirectResponse
     */
    public function show(Representative $representative): View|RedirectResponse
    {
        try {
            $this->authorize('view', $representative);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('representatives.show', compact('representative'));
    }

    /**
     * Show the form for creating a new representative.
     *
     * @return View|RedirectResponse
     */
    public function create(): View|RedirectResponse
    {
        try {
            $this->authorize('create', Representative::class);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('representatives.create');
    }

    /**
     * Store a newly created representative in storage.
     *
     * @param StoreRepresentativeRequest $request
     * @param StoreRepresentativeService $storeService
     * @return RedirectResponse
     */
    public function store(StoreRepresentativeRequest $request, StoreRepresentativeService $storeService): RedirectResponse
    {
        try {
            // Validación ya hecha por StoreRepresentativeRequest
            $representative = $storeService->handle($request->validated());

            return redirect()->route('students.create', ['representative' => $representative->id])
                ->with('status', '¡Representante registrado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified representative.
     *
     * @param Representative $representative
     * @return View|RedirectResponse
     */
    public function edit(Representative $representative): View|RedirectResponse
    {
        try {
            $this->authorize('edit', $representative);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('representatives.edit', compact('representative'));
    }

    /**
     * Update the specified representative in storage.
     *
     * @param UpdateRepresentativeRequest $request
     * @param UpdateRepresentativeService $updateService
     * @param Representative $representative
     * @return RedirectResponse
     */    public function update(UpdateRepresentativeRequest $request, UpdateRepresentativeService $updateService, Representative $representative): RedirectResponse
    {
        try {
            $representative = $updateService->handle($representative, $request->validated());
            return redirect()->route('representatives.show', $representative)
                ->with('status', '¡Representante actualizado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle the activation status of the specified representative.
     *
     * @param Representative $representative
     * @param RepresentativeActivationService $activationService
     * @return RedirectResponse
     */
    public function toggleActivation(Representative $representative, RepresentativeActivationService $activationService): RedirectResponse
    {
        try {
            // Delegate permission and toggle logic to the service
            $representative = $activationService->changeStatus($representative);

            // Return success message based on new status
            $status = $representative->is_active ? 'activado' : 'desactivado';

            return redirect()->route('representatives.show', $representative)
                ->with('status', "¡Representante {$status} correctamente!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
