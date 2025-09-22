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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Traits\AuthorizesRedirect;

class UserController extends Controller
{
    use AuthorizesRequests, AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * Display a listing of users.
     * Applies optional search, status, and role filters.
     */
    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', User::class, function () use ($request) {
            $roles  = Role::all();
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $role   = $request->input('role');

            // Only display if exists a search value.
            $query = User::searchWithFilters($search, $status, $role);
            $users = $query ? $query->paginate(6) : collect();

            return view('users.index', compact('users', 'roles'));
        });
    }

    /**
     * Display a single user.
     */
    public function show(User $user): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $user, function () use ($user) {
            return view('users.show', compact('user'));
        });
    }

    /**
     * Show the form to create a new user.
     * Only roles assignable by the current user are provided.
     */
    public function create(RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', User::class, function () use ($roleAssignmentService) {
            $roles = $roleAssignmentService->getAssignableRoles($this->currentUser());
            return view('users.create', compact('roles'));
        });
    }

    /**
     * Store a new user.
     * Delegates creation logic to the StoreUserService.
     */
    public function store(StoreUserRequest $request, StoreUserService $storeService): RedirectResponse
    {
        try {
            $user = $storeService->handle($request->validated());
            return redirect()->route('users.show', $user)
                ->with('status', '¡Usuario registrado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Hubo un error al registrar el usuario.');
        }
    }

    /**
     * Show the form to edit an existing user.
     * Only roles assignable by the current user are provided.
     */
    public function edit(User $user, RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('edit', $user, function () use ($user, $roleAssignmentService) {
            $roles = $roleAssignmentService->getAssignableRoles($this->currentUser());
            return view('users.edit', compact('user', 'roles'));
        });
    }

    /**
     * Update an existing user.
     * Delegates update logic to the UpdateUserService.
     */
    public function update(UpdateUserRequest $request, UpdateUserService $updateService, User $user): RedirectResponse
    {
        try {
            $user = $updateService->handle($user, $request->validated());
            return redirect()->route('users.show', $user)
                ->with('status', '¡Usuario actualizado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Hubo un error al actualizar el usuario.');
        }
    }

    /**
     * Delete a user along with their representative, if any.
     */
    public function destroy(User $user): RedirectResponse
    {
        return $this->authorizeOrRedirect('delete', $user, function () use ($user) {
            if ($user->representative?->exists()) {
                $user->representative()->delete();
            }
            $user->delete();
            return redirect()->route('users.index')
                ->with('status', '¡Usuario eliminado correctamente!');
        });
    }

    /**
     * Toggle the activation status of a user.
     * Delegates status change to UserActivationService.
     */
    public function toggleActivation(User $user, UserActivationService $activationService): RedirectResponse
    {
        try {
            $user = $activationService->changeStatus($user);
            $status = $user->is_active ? 'activado' : 'desactivado';
            return redirect()->route('users.show', $user)
                ->with('status', "¡Usuario {$status} correctamente!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
