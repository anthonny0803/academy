<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Services\RoleAssignmentService;
use App\Services\StoreUserService;
use App\Services\UpdateUserService;
use App\Services\UserActivationService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
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
     * Display a listing of the users.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $this->authorize('viewAny', User::class);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $roles = Role::all();
        $users = collect();
        $search = trim($request->input('search', '')); // ✅ Agrega trim() aquí

        if ($search !== '') {
            $query = User::with('roles')
                ->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));

            // Filtros opcionales de status y rol
            if ($request->filled('status') && $request->input('status') !== 'Todos') {
                $isActive = $request->input('status') === 'Activo' ? 1 : 0;
                $query->where('is_active', $isActive);
            }

            if ($request->filled('role') && $request->input('role') !== 'Todos') {
                $role = $request->input('role');
                $query->whereHas('roles', fn($q) => $q->where('name', $role));
            }

            $users = $query->paginate(6);
        }

        // Si $search es '' la variable $users nunca cambia, sigue siendo una colección vacía.



        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Display the specified user.
     *
     * @param User $user
     * @return View|RedirectResponse
     */
    public function show(User $user): View|RedirectResponse
    {
        try {
            $this->authorize('view', $user);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @param RoleAssignmentService $roleAssignmentService
     * @return View|RedirectResponse
     */
    public function create(RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        try {
            $this->authorize('create', User::class);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $roles = $roleAssignmentService->getAssignableRoles($this->currentUser());

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     * @param StoreUserService $storeService
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request, StoreUserService $storeService): RedirectResponse
    {
        try {
            $user = $storeService->handle($request->validated());
            return redirect()->route('users.show', $user)
                ->with('status', '¡Usuario registrado con éxito!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Hubo un error al registrar el usuario.');
        }
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @param RoleAssignmentService $roleAssignmentService
     * @return View|RedirectResponse
     */
    public function edit(User $user, RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        try {
            $this->authorize('edit', $user);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $roles = $roleAssignmentService->getAssignableRoles($this->currentUser());

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param UpdateUserRequest $request
     * @param UpdateUserService $updateService
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, UpdateUserService $updateService, User $user): RedirectResponse
    {
        try {
            $user = $updateService->handle($user, $request->validated());
            return redirect()->route('users.show', $user)
                ->with('status', '¡Usuario actualizado con éxito!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Hubo un error al actualizar el usuario.');
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->authorize('delete', $user);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if ($user->representative?->exists()) {
            $user->representative()->delete();
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('status', '¡Usuario eliminado con éxito!');
    }

    /**
     * Toggle the activation status of the specified user.
     *
     * @param User $user
     * @param UserActivationService $activationService
     * @return RedirectResponse
     */
    public function toggleActivation(User $user, UserActivationService $activationService): RedirectResponse
    {
        try {
            $status = $activationService->toggle($user);
            return redirect()->route('users.show', $user)
                ->with('status', "¡Usuario {$status} con éxito!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
