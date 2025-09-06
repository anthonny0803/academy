<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Services\RoleAssignmentService;
use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display the listing view.
     */
    public function index(Request $request): View
    {
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
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    /**
     * Display the creation view with assignable roles.
     */
    public function create(RoleAssignmentService $roleAssignmentService)
    {
        $roles = $roleAssignmentService->getAssignableRoles(Auth::user());

        return view('users.create', compact('roles'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(StoreUserRequest $request, UserService $userService)
    {
        try {
            $user = $userService->createUser($request->validated());

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
    public function edit(User $user): View|RedirectResponse
    {
        // Prevent editing of the developer user
        if ($user->id === 1) {
            return redirect()->route('users.index')->with('error', 'No se puede editar este usuario.');
        }

        // Prevent editing between same roles
        if (Auth::user()->hasRole('SuperAdmin') && Auth::user()->id !== 1 && $user->hasRole('SuperAdmin')) {
            return redirect()->route('users.index')->with('error', 'No tienes autorización para editar este usuario.');
        }

        // Prevent editing of lower roles
        if (Auth::user()->hasRole('Administrador') && ($user->hasRole('Administrador') || $user->hasRole('SuperAdmin'))) {
            return redirect()->route('users.index')->with('error', 'No tienes autorización para editar este usuario.');
        }

        // Filtra los roles según permisos del usuario autenticado
        $rolesQuery = Role::query();
        if (Auth::user()->hasRole('SuperAdmin')) {
            $rolesQuery->whereNotIn('name', ['Representante', 'Estudiante']);
        } elseif (Auth::user()->hasRole('Administrador')) {
            $rolesQuery->whereNotIn('name', ['SuperAdmin', 'Administrador', 'Representante', 'Estudiante']);
        }

        $roles = $rolesQuery->get();
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
        $currentUser = Auth::user(); // The current authenticated user
        $userIsRepresentative = $user->hasRole('Representante'); // Verify if the user has a Representative role

        // Prevent deletion of Developer
        if ($user->id === 1) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar este usuario.');
        }

        // Only allow Developer and SuperAdmin to delete users
        if ($currentUser->id !== 1 && !$currentUser->hasRole('SuperAdmin')) {
            return redirect()->route('users.index')->with('error', 'No tienes autorización para realizar esta acción.');
        }

        // Check if the user is active
        if ($user->is_active) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar un usuario activo.');
        }

        // Prevent deletion of self
        if ($currentUser->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // Prevent deletion of a SuperAdmin by another SuperAdmin except Developer
        if ($currentUser->hasRole('SuperAdmin') && $currentUser->id !== 1 && $user->hasRole('SuperAdmin')) {
            return redirect()->route('users.index')->with('error', 'No tienes autorización para eliminar este usuario.');
        }

        // If the user has a Representative role, and is active, prevent deletion
        if ($userIsRepresentative && $user->is_active) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar este usuario porque su rol de representante está activo.');
        }

        // If the user has a Representative role and has students, prevent deletion
        if ($userIsRepresentative && $user->representative?->students()->exists()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar este usuario porque su representante tiene estudiantes asociados.');
        }

        //  If the user is a Representative without students, delete the representative record first
        if ($userIsRepresentative && !$user->representative?->students()->exists()) {
            $user->representative()->delete();
        }

        // Delete the user if all checks passed
        $user->delete();

        // Redirect with success message
        return redirect()->route('users.index')
            ->with('status', '¡Usuario eliminado con éxito!');
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
