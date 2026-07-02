<?php

namespace App\Domains\AI\Policies;

use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Illuminate\Auth\Access\Response;

class StudentPerformanceObservationPolicy
{
    private function cannotAccessObservations(User $user): ?Response
    {
        if (! $user->isActive() || (! $user->isDeveloper() && ! $user->isSupervisor() && ! $user->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar observaciones de desempeño.');
        }

        return null;
    }

    public function viewAny(User $currentUser, Student $student): Response
    {
        return $this->cannotAccessObservations($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser, Student $student): Response
    {
        return $this->cannotAccessObservations($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser, StudentPerformanceObservation $observation): Response
    {
        return $this->cannotAccessObservations($currentUser)
            ?? Response::allow();
    }
}
