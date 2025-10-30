<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\RoleManagement\AssignRoleRequest;
use App\Models\User;
use App\Services\RoleManagement\AssignRoleService;
use App\Services\Users\RoleAssignmentService;
use App\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleManagementController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;

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
                $users = User::query()
                    ->search($search)
                    ->with(['roles', 'teacher', 'representative', 'student'])
                    ->orderBy('name')
                    ->orderBy('last_name')
                    ->paginate(10)
                    ->withQueryString();
            }

            return view('role-management.index', compact('users', 'search'));
        });
    }

    public function showAssignOptions(User $user): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('assignView', $user, function () use ($user) {
            $user->load(['roles', 'teacher', 'representative', 'student']);
            $availableRoles = $this->getAvailableRoles($user);

            return view('role-management.assign-options', compact('user', 'availableRoles'));
        });
    }

    public function showForm(User $user, string $role): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('assignView', $user, function () use ($user, $role) {
            $roleEnum = Role::from($role);

            $user->load(['roles', 'teacher', 'representative', 'student']);
            $missingFields = $this->getMissingFields($user, $roleEnum);

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
        // Validar con policy antes de asignar
        $this->authorize('assign', $user);
        
        $roleEnum = Role::from($role);
        $service->handle($user, $roleEnum, $request->validated());

        return redirect()
            ->route('role-management.show-assign-options', $user)
            ->with('success', "¡Rol {$roleEnum->value} asignado correctamente!");
    }

    private function assignDirect(User $user, Role $role): RedirectResponse
    {
        // Validar con policy antes de asignar
        $this->authorize('assign', $user);
        
        app(AssignRoleService::class)->handle($user, $role, []);

        return redirect()
            ->route('role-management.show-assign-options', $user)
            ->with('success', "¡Rol {$role->value} asignado correctamente!");
    }

    private function getAvailableRoles(User $user): array
    {
        $currentUser = $this->currentUser();
        $currentRoles = $user->roles->pluck('name')->toArray();
        
        $assignableRoles = app(RoleAssignmentService::class)
            ->getAssignableRolesForAdditionalAssignment($currentUser);
        
        $available = [];
        
        foreach ($assignableRoles as $spatieRole) {
            if (!in_array($spatieRole->name, $currentRoles)) {
                $roleEnum = Role::from($spatieRole->name);
                
                $available[] = [
                    'role' => $roleEnum,
                    'label' => $roleEnum->value,
                    'description' => match($roleEnum) {
                        Role::Supervisor => 'Rol administrativo superior',
                        Role::Admin => 'Rol administrativo',
                        Role::Teacher => 'Se creará perfil de profesor',
                        Role::Representative => 'Se creará perfil de representante',
                    },
                    'needs_form' => $this->roleNeedsForm($user, $roleEnum),
                ];
            }
        }
        
        return $available;
    }

    private function roleNeedsForm(User $user, Role $role): bool
    {
        return !empty($this->getMissingFields($user, $role));
    }

    private function getMissingFields(User $user, Role $role): array
    {
        $missing = [];

        if (empty($user->password)) {
            $missing[] = 'password';
        }

        if ($role === Role::Representative) {
            if (empty($user->document_id)) $missing[] = 'document_id';
            if (empty($user->birth_date)) $missing[] = 'birth_date';
            if (empty($user->phone)) $missing[] = 'phone';
            if (empty($user->address)) $missing[] = 'address';
        }

        return $missing;
    }
}