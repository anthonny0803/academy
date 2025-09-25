<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $currentUser): Response
    {
        if (!$currentUser->hasRole(['Supervisor', 'Administrador']) && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para ver el módulo de usuarios.');
        }

        return Response::allow();
    }

    public function view(User $currentUser, User $targetUser): Response
    {
        if (!$currentUser->hasRole(['Supervisor', 'Administrador']) && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->id === 1 && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para ver este usuario.');
        }

        if ($currentUser->hasRole('Administrador') && ($targetUser->hasRole('Supervisor'))) {
            return Response::deny('No tienes autorización para ver este usuario.');
        }

        return Response::allow();
    }

    public function create(User $currentUser): Response
    {
        if (!$currentUser->hasRole(['Supervisor', 'Administrador']) && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para crear usuarios.');
        }

        return Response::allow();
    }

    public function edit(User $currentUser, User $targetUser): Response
    {
        if (!$currentUser->hasRole(['Supervisor', 'Administrador']) && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->id === 1) {
            return Response::deny('No se puede modificar este usuario.');
        }

        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes modificar tu usuario.');
        }

        if ($currentUser->hasRole('Supervisor') && $currentUser->id !== 1 && $targetUser->hasRole('Supervisor')) {
            return Response::deny('No tienes autorización para modificar usuarios con tu rol.');
        }

        if ($currentUser->hasRole('Administrador') && ($targetUser->hasRole('Administrador') || $targetUser->hasRole('Supervisor'))) {
            return Response::deny('No tienes autorización para modificar usuarios con tu rol o roles superiores.');
        }

        return Response::allow();
    }

    public function delete(User $currentUser, User $targetUser): Response
    {
        if (!$currentUser->hasRole('Supervisor') && $currentUser->id !== 1) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->id === 1) {
            return Response::deny('No puedes eliminar este usuario.');
        }

        if ($currentUser->hasRole('Supervisor') && $currentUser->id !== 1 && $targetUser->hasRole('Supervisor')) {
            return Response::deny('No puedes eliminar usuarios con tu rol.');
        }

        if ($targetUser->is_active) {
            return Response::deny('No puedes eliminar un usuario activo.');
        }

        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes eliminar tu usuario.');
        }

        if ($currentUser && $targetUser->representative?->students()->exists()) {
            return Response::deny('No puedes eliminar a un usuario que tiene estudiantes.');
        }

        return Response::allow();
    }

    public function toggle(User $currentUser, User $targetUser): Response
    {
        if ($targetUser->id === 1) {
            return Response::deny('No se puede cambiar el estado de este usuario.');
        }

        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No tienes autorización para cambiar el estado de tu usuario.');
        }

        if ($currentUser->hasRole('Supervisor') && $currentUser->id !== 1 && $targetUser->hasRole('Supervisor')) {
            return Response::deny('No tienes autorización para cambiar el estado de este usuario.');
        }

        if ($currentUser->hasRole('Administrador') && $targetUser->hasRole(['Administrador', 'Supervisor'])) {
            return Response::deny('No tienes autorización para cambiar el estado de usuarios con mismo tu rol o superiores.');
        }

        return Response::allow();
    }
}
