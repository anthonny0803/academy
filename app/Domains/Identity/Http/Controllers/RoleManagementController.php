<?php

namespace App\Domains\Identity\Http\Controllers;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Http\Requests\RoleManagement\AssignRoleRequest;
use App\Domains\Identity\Models\User;
use App\Domains\Identity\Repositories\UserRepository;
use App\Domains\Identity\Services\RoleManagement\AssignRoleService;
use App\Domains\Identity\Services\RoleManagement\AvailableRolesService;
use App\Domains\Identity\Services\RoleManagement\RoleRequirementsService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleManagementController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;

    public function __construct(
        private UserRepository $userRepository
    ) {}

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('assignView', User::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));

            if (empty($search)) {
                $users = collect();
            } else {
                $users = $this->userRepository
                    ->paginateForRoleAssignment($search, 6)
                    ->withQueryString();
            }

            return view('role-management.index', compact('users', 'search'));
        });
    }

    public function showAssignOptions(User $user, AvailableRolesService $availableRolesService): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('assignManage', $user, function () use ($user, $availableRolesService) {
            $user->load(['roles', 'teacher', 'representative', 'student']);
            $availableRoles = $availableRolesService->forUser($user, $this->currentUser());

            return view('role-management.assign-options', compact('user', 'availableRoles'));
        });
    }

    public function showForm(User $user, string $role, RoleRequirementsService $roleRequirements): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('assignManage', $user, function () use ($user, $role, $roleRequirements) {
            $roleEnum = Role::from($role);

            $user->load(['roles', 'teacher', 'representative', 'student']);
            $missingFields = $roleRequirements->missingFieldsForRole($user, $roleEnum);

            if (empty($missingFields)) {
                return $this->assignDirect($user, $roleEnum);
            }

            return view('role-management.assign-form', compact('user', 'roleEnum', 'missingFields'));
        });
    }

    public function assign(
        AssignRoleRequest $request,
        AssignRoleService $service,
        User $user,
        string $role
    ): RedirectResponse {
        $this->authorize('assign', $user);

        $roleEnum = Role::from($role);
        $service->handle($user, $roleEnum, $request->validated());

        return redirect()
            ->route('role-management.show-assign-options', $user)
            ->with('success', "¡Rol {$roleEnum->value} asignado correctamente!");
    }

    private function assignDirect(User $user, Role $role): RedirectResponse
    {
        $this->authorize('assign', [$user, $role]);

        app(AssignRoleService::class)->handle($user, $role, []);

        return redirect()
            ->route('role-management.show-assign-options', $user)
            ->with('success', "¡Rol {$role->value} asignado correctamente!");
    }
}
