<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\AuthorizesRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    use AuthorizesRedirect;
    
    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', User::class, function () use ($request) {
            $roles  = Role::all();
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $role   = $request->input('role');

            // Security: Only display if exists a search value.
            if (empty($search)) {
                $users = collect();
            } else {
                $users = User::query()
                    ->search($search)
                    ->when($status && $status !== 'Todos', function ($q) use ($status) {
                        $status === 'Activo' ? $q->active() : $q->inactive();
                    })
                    ->when($role && $role !== 'Todos', fn($q) => $q->withRole($role))
                    ->with('roles')
                    ->orderByName()
                    ->paginate(5)
                    ->withQueryString();
            }

            return view('users.index', compact('users', 'roles'));
        });
    }
}
