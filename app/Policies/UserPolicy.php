<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $currentUser): bool
    {
        // If the current user is not an authenticated user with view permissions
        if (!$currentUser->hasRole(['SuperAdmin', 'Administrador']) && $currentUser->id !== 1) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $currentUser, User $targetUser): bool
    {
        // If the current user is not an authenticated user with view permissions
        if (!$currentUser->hasRole(['SuperAdmin', 'Administrador']) && $currentUser->id !== 1) {
            return false;
        }
        // Prevent viewing of the developer user by others
        if ($targetUser->id === 1 && $currentUser->id !== 1) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $currentUser): bool
    {
        // If the current user is not an authenticated user with create permissions
        if (!$currentUser->hasRole(['SuperAdmin', 'Administrador']) && $currentUser->id !== 1) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function edit(User $currentUser, User $targetUser): bool
    {
        // Prevent editing of the developer user
        if ($targetUser->id === 1) {
            return false;
        }

        // Prevent editing between same roles except for developer
        if ($currentUser->hasRole('SuperAdmin') && $currentUser->id !== 1 && $targetUser->hasRole('SuperAdmin')) {
            return false;
        }

        // Prevent editing of lower roles
        if ($currentUser->hasRole('Administrador') && ($targetUser->hasRole('Administrador') || $targetUser->hasRole('SuperAdmin'))) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $currentUser, User $targetUser): Response
    {
        // Only Developer and SuperAdmin can delete users
        if (!$currentUser->hasRole('SuperAdmin') || $currentUser->id === 1) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        // Prevent deletion of the developer user
        if ($targetUser->id === 1) {
            return Response::deny('No puedes eliminar al usuario desarrollador.');
        }

        // Prevent deletion of active users
        if ($targetUser->is_active) {
            return Response::deny('No puedes eliminar a un usuario activo.');
        }

        // Prevent deletion of a SuperAdmin by another SuperAdmin except Developer
        if ($currentUser->hasRole('SuperAdmin') && $currentUser->id !== 1 && $targetUser->hasRole('SuperAdmin')) {
            return Response::deny('No puedes eliminar a este usuario.');
        }

        // Prevent deletion of self
        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes eliminar a tu usuario.');
        }

        // If the user has a Representative role and has students, prevent deletion
        if ($currentUser && $targetUser->representative?->students()->exists()) {
            return Response::deny('No puedes eliminar a un usuario que tiene estudiantes.');
        }

        return Response::allow();
    }
}
