<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserActivationService
{
    /**
     * Check the authorization before changing user status.
     *
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function changeStatus(User $user): User
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

        // Supervisor cannot change the status of other Supervisors except Developer
        if ($currentUser->hasRole('Supervisor') && $currentUser->id !== 1 && $user->hasRole('Supervisor')) {
            throw new \Exception('No tienes autorización para cambiar el estado de este usuario.');
        }

        // Administrador cannot change higher or same roles
        if ($currentUser->hasRole('Administrador') && $user->hasRole(['Administrador', 'Supervisor'])) {
            throw new \Exception('No tienes autorización para cambiar el estado de usuarios con mismo tu rol o superiores.');
        }

        // Toggle activation status
        $user->activation(!$user->is_active);

        return $user;
    }
}
