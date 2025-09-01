<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display the listing view.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles'); // eager load de roles para evitar N+1

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $request->filled('search') ? $query->paginate(6) : collect();

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
     * Display the creation view.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users')],
            'sex' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => strtoupper($request->name),
            'last_name' => strtoupper($request->last_name),
            'email' => strtolower($request->email),
            'sex' => $request->sex,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Asignar rol
        $roleName = $request->input('role');
        $user->assignRole($roleName);

        event(new Registered($user));

        return redirect()->route('users.show', $user)->with('status', '¡Usuario registrado con éxito!');
    }

    /**
     * Display the edit user view.
     */
    public function edit(User $user): View
    {
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

        // Actualizar datos del usuario
        $user->update([
            'name' => strtoupper($request->name),
            'last_name' => strtoupper($request->last_name),
            'email' => strtolower($request->email),
            'sex' => $request->sex,
        ]);

        // Actualizar roles
        $rolesFromForm = $request->input('roles', []);

        // Definimos los roles “editables” (empleados)
        $editableRoles = Role::whereIn('name', $rolesFromForm)->pluck('name')->toArray();

        // Roles existentes que no se pueden tocar (clientes)
        $nonEditableRoles = $user->roles->whereNotIn('name', ['SuperAdmin', 'Administrador', 'Profesor'])->pluck('name')->toArray();

        // Sincronizamos todos los roles: los editables + los que no se tocan
        $user->syncRoles(array_merge($editableRoles, $nonEditableRoles));

        // Activar si tiene al menos un rol de empleado
        $employeeRoles = ['SuperAdmin', 'Administrador', 'Profesor'];
        $user->is_active = $user->roles->pluck('name')->intersect($employeeRoles)->isNotEmpty();

        $user->save();


        return redirect()->route('users.show', $user)->with('status', '¡Usuario actualizado con éxito!');
    }
}
