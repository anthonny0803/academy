<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SectionPolicy
{
    // Helper Methods

    private function cannotViewSections(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para ver secciones.');
        }

        return null;
    }

    private function cannotManageSections(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para gestionar secciones.');
        }

        return null;
    }

    private function cannotDeleteActiveSection(Section $section): ?Response
    {
        if ($section->isActive()) {
            return Response::deny('No puedes eliminar una sección activa. Desactívala primero.');
        }

        return null;
    }

    private function cannotDeleteSectionWithEnrollments(Section $section): ?Response
    {
        if ($section->enrollments()->exists()) {
            return Response::deny('No puedes eliminar una sección con inscripciones registradas.');
        }

        return null;
    }

    private function cannotDeleteSectionWithAssignments(Section $section): ?Response
    {
        if ($section->sectionSubjectTeachers()->exists()) {
            return Response::deny('No puedes eliminar una sección con asignaciones de profesores.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewSections($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser, Section $section): Response
    {
        return $this->cannotViewSections($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageSections($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser, Section $section): Response
    {
        return $this->cannotManageSections($currentUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser, Section $section): Response
    {
        return $this->cannotManageSections($currentUser)
            ?? $this->cannotDeleteActiveSection($section)
            ?? $this->cannotDeleteSectionWithEnrollments($section)
            ?? $this->cannotDeleteSectionWithAssignments($section)
            ?? Response::allow();
    }

    public function toggle(User $currentUser, Section $section): Response
    {
        return $this->cannotManageSections($currentUser)
            ?? Response::allow();
    }
}
