<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Services\RoleAssignmentService;
use App\Services\StoreUserService;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get the currently authenticated user.
     */
    protected function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * Display the listing view.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // If the current user cannot view users, redirect with error
        if (!$this->currentUser()->can('viewAny', User::class)) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes autorización para ver usuarios.');
        }

        // Search functionality
        $users = collect(); // Collection empty by default
        if ($request->filled('search')) {
            $search = $request->input('search');
            $users = User::with('roles')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->paginate(6);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View|RedirectResponse
    {
        // If the current user cannot view the target user, redirect with error
        if (!$this->currentUser()->can('view', $user)) {
            return redirect()->route('users.index')
                ->with('error', 'No tienes autorización para ver este usuario.');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Display the creation view with assignable roles.
     */
    public function create(RoleAssignmentService $roleAssignmentService)
    {
        // If the current user cannot create users, redirect with error
        if (!$this->currentUser()->can('create', User::class)) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes autorización para crear usuarios.');
        }
        $roles = $roleAssignmentService->getAssignableRoles(Auth::user());

        return view('users.create', compact('roles'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(StoreUserRequest $request, StoreUserService $storeService)
    {
        try {
            $user = $storeService->handle($request->validated());

            return redirect()->route('users.show', $user)
                ->with('status', '¡Usuario registrado con éxito!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hubo un error al registrar el usuario.');
        }
    }

    /**
     * Display the edit user view.
     */
    public function edit(User $user, RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        // If the current user cannot edit the target user, redirect with error
        if (!$this->currentUser()->can('edit', $user)) {
            return redirect()->route('users.index')
                ->with('error', 'No tienes autorización para modificar este usuario.');
        }

        // Get assignable roles based on current user's role
        $roles = $roleAssignmentService->getAssignableRoles($this->currentUser());

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->ignore($user->id),
            ],
            'sex' => ['required', 'string', 'max:15'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        // Update user data
        $user->update([
            'name' => strtoupper($request->name),
            'last_name' => strtoupper($request->last_name),
            'email' => strtolower($request->email),
            'sex' => $request->sex,
        ]);

        // Update roles
        $rolesFromForm = $request->input('roles', []);

        // Definimos los roles “editables” (empleados)
        $editableRoles = Role::whereIn('name', $rolesFromForm)->pluck('name')->toArray();

        // Roles existentes que no se pueden tocar (clientes)
        $nonEditableRoles = $user->roles->whereNotIn('name', ['SuperAdmin', 'Administrador', 'Profesor'])->pluck('name')->toArray();

        // Sincronizamos todos los roles: los editables + los que no se tocan
        $user->syncRoles(array_merge($editableRoles, $nonEditableRoles));

        // Activate if has at least one employee role
        $employeeRoles = ['SuperAdmin', 'Administrador', 'Profesor'];
        $user->is_active = $user->roles->pluck('name')->intersect($employeeRoles)->isNotEmpty();

        $user->save();


        return redirect()->route('users.show', $user)->with('status', '¡Usuario actualizado con éxito!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->authorize('delete', $user);

            // If the user has a representative profile, delete it first
            if ($user->representative?->exists()) {
                $user->representative()->delete();
            }

            $user->delete();

            return redirect()->route('users.index')
                ->with('status', '¡Usuario eliminado con éxito!');
        } catch (AuthorizationException $e) {
            return redirect()->route('users.index')
                ->with('error', $e->getMessage()); // Catch and display the authorization error message
        }
    }

    /**
     * Toggle the activation status of the specified user.
     */
    public function toggleActivation(User $user): RedirectResponse
    {
        // Prevent changing status of Developer
        if ($user->id === 1) {
            return redirect()->route('users.index')->with('error', 'No se puede cambiar el estado de este usuario.');
        }

        // Cannot change own status
        if (Auth::user()->id === $user->id) {
            return $this->denied();
        }

        // SuperAdmin cannot change other SuperAdmins except Developer
        if (Auth::user()->hasRole('SuperAdmin') && Auth::user()->id !== 1 && $user->hasRole('SuperAdmin')) {
            return $this->denied();
        }

        // Administrador cannot change higher or same roles
        if (Auth::user()->hasRole('Administrador') && $user->hasRole(['Administrador', 'SuperAdmin'])) {
            return $this->denied();
        }

        // If user passes all checks, toggle status
        return $this->doToggle($user);
    }

    /**
     * Helper method to toggle user activation status
     */
    private function doToggle(User $user): RedirectResponse
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $statusMessage = $user->is_active ? 'activado' : 'desactivado';

        return redirect()->route('users.show', $user)->with('status', "¡Usuario {$statusMessage} con éxito!");
    }

    /**
     * Helper method to handle unauthorized actions
     */
    private function denied(): RedirectResponse
    {
        return redirect()->route('users.index')->with('error', 'No tienes autorización para realizar esta acción.');
    }
}
