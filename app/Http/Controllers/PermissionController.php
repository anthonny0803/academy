<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index(){

        $permissions = Permission::with('roles')->get();

        return (view('permissions.index', compact('permissions')));
    }
}
