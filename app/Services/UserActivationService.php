<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserActivationService
{
    public function toggle(User $user): string
    {
        $currentUser = Auth::user();

        // Prevent changing status of Developer
        if ($user->id === 1) {
            throw new \Exception('No se puede cambiar el estado de este usuario.');
        }

        // Cannot change own status
        if ($currentUser->id === $user->id) {
            throw new \Exception('No tienes autorización para cambiar el estado de tu usuario.');
        }

        // SuperAdmin cannot change the status of other SuperAdmins except Developer
        if ($currentUser->hasRole('SuperAdmin') && $currentUser->id !== 1 && $user->hasRole('SuperAdmin')) {
            throw new \Exception('No tienes autorización para cambiar el estado de este usuario.');
        }

        // Administrador cannot change higher or same roles
        if ($currentUser->hasRole('Administrador') && $user->hasRole(['Administrador', 'SuperAdmin'])) {
            throw new \Exception('No tienes autorización para cambiar el estado de usuarios con mismo tu rol o superiores.');
        }

        // Toggle status
        $user->is_active = !$user->is_active;
        $user->save();

        return $user->is_active ? 'activado' : 'desactivado';
    }
}
