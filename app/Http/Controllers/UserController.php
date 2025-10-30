<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Sex;
use App\Enums\Role as RoleEnum;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Services\Users\RoleAssignmentService;
use App\Services\Users\StoreUserService;
use App\Services\Users\UpdateUserService;
use App\Services\Users\DeleteUserService;
use App\Traits\AuthorizesRedirect;
use App\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;
    use CanToggleActivation;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', User::class, function () use ($request) {
            $employeeRoleNames = [
                RoleEnum::Supervisor->value,
                RoleEnum::Admin->value,
                RoleEnum::Teacher->value,
            ];
            $roles = Role::whereIn('name', $employeeRoleNames)->get();

            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');
            $role   = $request->input('role');

            // If no search term, return empty collection
            if (empty($search)) {
                $users = collect();
            } else {
                $users = User::query()
                    ->employees()
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
            $sexes = Sex::toArray();
            return view('users.create', compact('roles', 'sexes'));
        });
    }

    public function store(StoreUserRequest $request, StoreUserService $storeService): RedirectResponse
    {
        return $this->authorizeOrRedirect('create', User::class, function () use ($request, $storeService) {
            $user = $storeService->handle($request->validated());
            return redirect()->route('users.show', $user)
                ->with('success', '¡Usuario registrado correctamente!');
        });
    }

    public function edit(User $user): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $user, function () use ($user) {
            $roles = Role::all();
            return view('users.edit', compact('user', 'roles'));
        });
    }

    public function update(UpdateUserRequest $request, UpdateUserService $updateService, User $user): RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $user, function () use ($request, $updateService, $user) {
            $user = $updateService->handle($user, $request->validated());
            return redirect()
                ->route('users.show', $user)
                ->with('success', '¡Usuario actualizado correctamente!');
        });
    }

    public function destroy(User $user, DeleteUserService $deleteService): RedirectResponse
    {
        return $this->authorizeOrRedirect('delete', $user, function () use ($user, $deleteService) {
            $deleteService->handle($user);

            return redirect()
                ->route('users.index')
                ->with('success', '¡Usuario eliminado correctamente!');
        });
    }

    public function toggleActivation(User $user): RedirectResponse
    {
        return $this->executeToggle($user);
    }
}
