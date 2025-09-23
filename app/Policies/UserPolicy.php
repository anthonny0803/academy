<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param User $currentUser
     * @return Response
     */
    public function viewAny(User $currentUser): Response
    {
        // If the current user is not an authenticated user with view permissions
        if (!$currentUser->hasRole(['Supervisor', 'Administrador']) && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para ver el módulo de usuarios.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $currentUser
     * @param User $targetUser
     * @return Response
     */
    public function view(User $currentUser, User $targetUser): Response
    {
        // If the current user cannot view the target user, redirect with error
        if (!$currentUser->hasRole(['Supervisor', 'Administrador']) && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        // Prevent viewing of the developer user by others
        if ($targetUser->id === 1 && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para ver este usuario.');
        }

        // Prevent viewing of lower roles
        if ($currentUser->hasRole('Administrador') && ($targetUser->hasRole('Supervisor'))) {
            return Response::deny('No tienes autorización para ver este usuario.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $currentUser
     * @return Response
     */
    public function create(User $currentUser): Response
    {
        // If the current user is not an authenticated user with create permissions
        if (!$currentUser->hasRole(['Supervisor', 'Administrador']) && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para crear usuarios.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can edit the model.
     * @param User $currentUser
     * @param User $targetUser
     * @return Response
     */
    public function edit(User $currentUser, User $targetUser): Response
    {

        // Only Developer, Supervisor and Administrador can edit users
        if (!$currentUser->hasRole(['Supervisor', 'Administrador']) && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        // Prevent editing of the developer user
        if ($targetUser->id === 1) {
            return Response::deny('No se puede modificar este usuario.');
        }

        // Prevent editing of self
        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes modificar tu usuario.');
        }

        // Prevent editing between same roles except for developer
        if ($currentUser->hasRole('Supervisor') && $currentUser->id !== 1 && $targetUser->hasRole('Supervisor')) {
            return Response::deny('No tienes autorización para modificar usuarios con tu rol.');
        }

        // Prevent editing of lower roles
        if ($currentUser->hasRole('Administrador') && ($targetUser->hasRole('Administrador') || $targetUser->hasRole('Supervisor'))) {
            return Response::deny('No tienes autorización para modificar usuarios con tu rol o roles superiores.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $currentUser
     * @param User $targetUser
     * @return Response
     */
    public function delete(User $currentUser, User $targetUser): Response
    {
        // Only Developer and Supervisor can delete users
        if (!$currentUser->hasRole('Supervisor') && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        // Prevent deletion of the developer user
        if ($targetUser->id === 1) {
            return Response::deny('No puedes eliminar este usuario.');
        }

        // Prevent deletion of a Supervisor by another Supervisor except Developer
        if ($currentUser->hasRole('Supervisor') && $currentUser->id !== 1 && $targetUser->hasRole('Supervisor')) {
            return Response::deny('No puedes eliminar usuarios con tu rol.');
        }

        // Prevent deletion of active users
        if ($targetUser->is_active) {
            return Response::deny('No puedes eliminar un usuario activo.');
        }

        // Prevent deletion of self
        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes eliminar tu usuario.');
        }

        // If the user has a Representative role and has students, prevent deletion
        if ($currentUser && $targetUser->representative?->students()->exists()) {
            return Response::deny('No puedes eliminar a un usuario que tiene estudiantes.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can toggle the activation status of the model.
     * @param User $currentUser
     * @param User $targetUser
     * @return Response
     */
    public function toggle(User $currentUser, User $targetUser): Response
    {
        // Prevent changing status of Developer
        if ($targetUser->id === 1) {
            return Response::deny('No se puede cambiar el estado de este usuario.');
        }

        // Cannot change own status
        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No tienes autorización para cambiar el estado de tu usuario.');
        }

        // Supervisor cannot change the status of other Supervisors except Developer
        if ($currentUser->hasRole('Supervisor') && $currentUser->id !== 1 && $targetUser->hasRole('Supervisor')) {
            return Response::deny('No tienes autorización para cambiar el estado de este usuario.');
        }

        // Administrador cannot change higher or same roles
        if ($currentUser->hasRole('Administrador') && $targetUser->hasRole(['Administrador', 'Supervisor'])) {
            return Response::deny('No tienes autorización para cambiar el estado de usuarios con mismo tu rol o superiores.');
        }

        return Response::allow();
    }
}
