<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Mostrar lista de Usuarios
    public function index()
    {
        // Agregar el rol al Usuario
        $users = User::with('role')->get();
        return view('users.index', compact('users'));
    }

    // Mostrar formulario para crear un nuevo Usuario
    public function create()
    {
        // Traer Roles para seleccionar
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // Crear Usuario
    public function store(Request $request)
    {
        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'username' => 'required|string|max:30|unique:users,username',
            'email' => 'required|email|max:30|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // La contraseña se hashea en el modelo
            'role_id' => 'required|exists:roles,id'
        ]);

        // Actualizar estado del Usuario antes de registrar
        $validated['status'] = 'active';

        // Registrar Usuario
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    // Mostrar formulario para actualizar el Usuario
    public function edit(User $user)
    {
        // Traer Roles para seleccionar
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    // Actualizar Usuario
    public function update(Request $request, User $user)
    {
        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        // Si hay nueva contraseña de Usuario
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:6|confirmed',
            ]);

            // Hashear contraseña
            $validated['password'] = Hash::make($request->password);
        }

        // Actualizar
        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar Usuario
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}
