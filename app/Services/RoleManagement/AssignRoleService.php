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
            
            // Validar duplicados SOLO para roles de perfil (Teacher, Representative)
            // Los roles administrativos (Supervisor, Admin) hacen SWAP automático
            if (in_array($role, Role::profileRoles()) && $user->hasRole($role->value)) {
                throw new \Exception("El usuario ya tiene el rol {$role->value}");
            }

            // Actualizar password si viene en los datos
            $this->updatePasswordIfNeeded($user, $data);

            // Lógica específica según el rol
            match ($role) {
                Role::Supervisor, Role::Admin => $this->handleAdministrativeRoleSwap($user, $role),
                Role::Teacher => $this->handleTeacherRole($user),
                Role::Representative => $this->handleRepresentativeRole($user, $data),
                default => throw new \Exception("Rol {$role->value} no soportado para asignación"),
            };

            // Retornar usuario con relaciones actualizadas
            return $user->fresh(['roles', 'teacher', 'representative', 'student']);
        });
    }

    private function handleAdministrativeRoleSwap(User $user, Role $newRole): void
    {
        // Obtener roles de perfil actuales (Teacher, Representative, Student)
        $profileRoles = $user->getRoleNames()
            ->filter(fn($roleName) => in_array($roleName, [
                Role::Teacher->value,
                Role::Representative->value,
                Role::Student->value,
            ]))
            ->toArray();

        // SWAP: Reemplaza rol administrativo, mantiene perfiles
        $rolesToSync = array_merge([$newRole->value], $profileRoles);
        $user->syncRoles($rolesToSync);

        // Activar usuario si es necesario
        if (!$user->is_active) {
            $user->update(['is_active' => true]);
        }
    }

    private function handleTeacherRole(User $user): void
    {
        // Verificar que no tenga ya un perfil Teacher
        if ($user->teacher()->exists()) {
            throw new \Exception('El usuario ya tiene un perfil de profesor');
        }

        // Asignar el rol de Spatie
        $user->assignRole(Role::Teacher->value);

        // Crear perfil Teacher
        Teacher::create([
            'user_id' => $user->id,
            'is_active' => true, // Permite acceso al sistema
        ]);
    }

    private function handleRepresentativeRole(User $user, array $data): void
    {
        // Verificar que no tenga ya un perfil Representative
        if ($user->representative()->exists()) {
            throw new \Exception('El usuario ya tiene un perfil de representante');
        }

        // Actualizar campos de User si vienen en $data
        $this->updateUserFields($user, $data);

        // Asignar el rol de Spatie
        $user->assignRole(Role::Representative->value);

        // Crear perfil Representative
        Representative::create([
            'user_id' => $user->id,
            'is_active' => true,
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

        // Solo actualizar si el campo viene en $data Y el user no lo tiene
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