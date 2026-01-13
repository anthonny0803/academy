<?php

namespace App\Services\RoleManagement;

use App\Enums\Role;
use App\Models\Representative;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignRoleService
{
    public function handle(User $user, Role $role, array $data): User
    {
        return DB::transaction(function () use ($user, $role, $data) {
            
            // Validate existing roles
            // Administrative roles (Supervisor, Admin) can be swapped
            if (in_array($role, Role::profileRoles()) && $user->hasRole($role->value)) {
                throw new \Exception("El usuario ya tiene el rol {$role->value}");
            }

            // Update password if provided
            $this->updatePasswordIfNeeded($user, $data);

            // Specific logic per role
            match ($role) {
                Role::Supervisor, Role::Admin => $this->handleAdministrativeRoleSwap($user, $role),
                Role::Teacher => $this->handleTeacherRole($user),
                Role::Representative => $this->handleRepresentativeRole($user, $data),
                default => throw new \Exception("Rol {$role->value} no soportado para asignación"),
            };

            // Return updated user with roles loaded
            return $user->fresh(['roles', 'teacher', 'representative', 'student']);
        });
    }

    private function handleAdministrativeRoleSwap(User $user, Role $newRole): void
    {
        // Get current profile roles (Teacher, Representative, Student)
        $profileRoles = $user->getRoleNames()
            ->filter(fn($roleName) => in_array($roleName, [
                Role::Teacher->value,
                Role::Representative->value,
                Role::Student->value,
            ]))
            ->toArray();

        // SWAP: Change to the new administrative role while keeping profile roles
        $rolesToSync = array_merge([$newRole->value], $profileRoles);
        $user->syncRoles($rolesToSync);

        // Activate user if not already active
        if (!$user->is_active) {
            $user->update(['is_active' => true]);
        }
    }

    private function handleTeacherRole(User $user): void
    {
        // Verify the user does not already have a Teacher profile
        if ($user->teacher()->exists()) {
            throw new \Exception('El usuario ya tiene un perfil de profesor');
        }

        // Assign the Spatie role
        $user->assignRole(Role::Teacher->value);

        // Create Teacher profile
        Teacher::create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
    }

    private function handleRepresentativeRole(User $user, array $data): void
    {
        // Verify the user does not already have a Representative profile
        if ($user->representative()->exists()) {
            throw new \Exception('El usuario ya tiene un perfil de representante');
        }

        // Update user fields if they are missing
        $this->updateUserFields($user, $data);

        // Assign the Spatie role
        $user->assignRole(Role::Representative->value);

        // Create Representative profile
        Representative::create([
            'user_id' => $user->id,
            'is_active' => false, // Inactive until store a student associated
        ]);
    }

    private function updatePasswordIfNeeded(User $user, array $data): void
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $user->update([
                'password' => $data['password'],
            ]);
        }
    }

    private function updateUserFields(User $user, array $data): void
    {
        $updates = [];

        // Only update fields that are provided and currently empty
        foreach (['document_id', 'birth_date', 'phone', 'address', 'occupation'] as $field) {
            if (isset($data[$field]) && empty($user->$field)) {
                $updates[$field] = $data[$field];
            }
        }

        if (!empty($updates)) {
            $user->update($updates);
        }
    }
}