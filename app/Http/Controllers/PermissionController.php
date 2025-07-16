<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Muestra todos los permisos disponibles.
     */
    public function index()
    {
        $permissions = Permission::with('roles')->get();
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Muestra el formulario para crear un nuevo permiso.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Almacena un nuevo permiso en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:permissions,name',
            'description' => 'required|string|max:30'
        ]);

        Permission::create($request->only([
            'name',
            'description'
        ]));

        return redirect()->route('permissions.index')->with('success', 'Permiso creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un permiso.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Actualiza un permiso en la base de datos.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:permissions,name,' . $permission->id,
            'description' => 'required|string|max:30'
        ]);

        $permission->update($request->only(['name', 'description']));

        return redirect()->route('permissions.index')
                         ->with('success', 'Permiso actualizado correctamente.');
    }

    /**
     * Elimina un permiso de la base de datos.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
                         ->with('success', 'Permiso eliminado correctamente.');
    }
}
