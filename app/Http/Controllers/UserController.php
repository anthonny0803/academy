<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Muestra una lista de los usuarios.
     */
    public function index()
    {
        // Obtener una lista de usuarios con 10 registros max
        $users = User::paginate(10);

        // Retornar la vista 'users.index' y pasarle la colección de usuarios
        return view('users.index', compact('users'));
    }

    /**
     * Muestra un usuario por Id.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Formulario para crear un usuario.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Función para crear el usuario.
     */
    public function store(Request $request)
    {
        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'document_id' => 'required|string|max:15',
            'username' => 'required|string|max:30|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // La contraseña se hashea en el modelo
            'sex' => 'required|string|in:Masculino,Femenino,Otro',
        ]);

        // Actualizar estado del usuario antes de registrar
        $validated['is_active'] = true;

        // Registrar
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Formulario para modificar un usuario.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Función para actualizar el usuario.
     */
    public function update(Request $request, User $user)
    {
        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'document_id' => 'required|string|max:15|unique:users,document_id,' . $user->id,
            'username' => 'required|string|max:30|unique:users,username,' . $user->id,
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'sex' => 'required|string|in:Masculino,Femenino,Otro',
            'is_active' => 'required|boolean',
        ]);

        // Si hay nueva contraseña de Usuario
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        // Actualizar la validación con los datos de password
        $validated = $request->validate($rules);

            // Actualizar usuario
            $user->update($validated);
            return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
        
    }

    /**
     * Función para eliminar el usuario.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}
