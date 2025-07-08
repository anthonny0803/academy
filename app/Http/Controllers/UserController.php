<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Muestra una lista de todos los usuarios.
     */
    public function index()
    {
        // Puedes incluir el rol con eager loading
        $users = User::with('role')->get();

        return view('users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        // Traemos los roles para que el admin seleccione uno
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        // Validaciones básicas
        $request->validate([
            'name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'username' => 'required|string|max:30|unique:users',
            'email' => 'required|email|max:30|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Creamos el usuario
        User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hasheamos la contraseña
            'role_id' => $request->role_id,
            'status' => 'active',
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza un usuario en la base de datos.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:6|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina un usuario de la base de datos.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}
