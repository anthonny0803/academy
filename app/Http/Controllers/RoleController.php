<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Muestra una lista de todos los roles.
     */
    public function index()
    {
        
        $roles = Role::with('permissions')->get();

        return view('roles.index', compact('roles'));
    }
}
