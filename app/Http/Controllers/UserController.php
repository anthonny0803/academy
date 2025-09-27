<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Services\RoleAssignmentService;
use App\Services\StoreUserService;
use App\Services\UpdateUserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Traits\AuthorizesRedirect;

class UserController extends Controller
{
    use AuthorizesRequests, AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', User::class, function () use ($request) {
            $roles  = Role::all();
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $role   = $request->input('role');

            // Only display if exists a search value.
            $query = User::searchWithFilters($search, $status, $role);
            $users = $query ? $query->paginate(5) : collect();

            return view('users.index', compact('users', 'roles'));
        });
    }

    public function show(User $user): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('view', $user, function () use ($user) {
            return view('users.show', compact('user'));
        });
    }

    public function create(RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('create', User::class, function () use ($roleAssignmentService) {
            $roles = $roleAssignmentService->getAssignableRoles($this->currentUser());
            return view('users.create', compact('roles'));
        });
    }

    public function store(StoreUserRequest $request, StoreUserService $storeService): RedirectResponse
    {
        try {
            $user = $storeService->handle($request->validated());
            return redirect()->route('users.show', $user)
                ->with('success', '¡Usuario registrado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(User $user, RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('edit', $user, function () use ($user, $roleAssignmentService) {
            $roles = $roleAssignmentService->getAssignableRoles($this->currentUser());
            return view('users.edit', compact('user', 'roles'));
        });
    }

    public function update(UpdateUserRequest $request, UpdateUserService $updateService, User $user): RedirectResponse
    {
        try {
            $user = $updateService->handle($user, $request->validated());
            return redirect()->route('users.show', $user)
                ->with('success', '¡Usuario actualizado correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        return $this->authorizeOrRedirect('delete', $user, function () use ($user) {
            if ($user->isRepresentative() && !$user->representative->hasStudents()) {
                $user->representative()->delete();
            }
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', '¡Usuario eliminado correctamente!');
        });
    }

    public function toggleActivation(User $user): RedirectResponse
    {
        return $this->authorizeOrRedirect('toggle', $user, function () use ($user) {
            $user->activation(!$user->is_active);
            $status = $user->is_active ? 'activado' : 'desactivado';

            return redirect()->route('users.show', $user)
                ->with('success', "¡Usuario {$status} correctamente!");
        });
    }
}
