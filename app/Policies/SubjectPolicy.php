<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubjectPolicy
{
    private function isActiveWithHighRole(User $user): bool
    {
        return $user->isActive() && ($user->isSupervisor() || $user->isDeveloper());
    }

    public function viewAny(User $currentUser): Response
    {
        return $this->isActiveWithHighRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de asignaturas.');
    }
}
