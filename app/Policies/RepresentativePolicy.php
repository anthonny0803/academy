<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class RepresentativePolicy
{
    private function cannotManageRepresentatives(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar representantes.');
        }

        return null;
    }

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotManageRepresentatives($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser): Response
    {
        return $this->cannotManageRepresentatives($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageRepresentatives($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser): Response
    {
        return $this->cannotManageRepresentatives($currentUser)
            ?? Response::allow();
    }
}
