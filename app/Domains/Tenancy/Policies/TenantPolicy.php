<?php

namespace App\Domains\Tenancy\Policies;

use App\Domains\Identity\Models\User;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Scopes\TenantScope;
use Illuminate\Auth\Access\Response;

class TenantPolicy
{
    // Helper Methods

    private function cannotManageTenants(User $user): ?Response
    {
        if (! $user->isActive() || ! $user->isDeveloper()) {
            return Response::deny('No tienes autorización para gestionar organizaciones.');
        }

        return null;
    }

    private function cannotDeleteTenantWithUsers(Tenant $tenant): ?Response
    {
        if ($tenant->users()->withoutGlobalScope(TenantScope::class)->exists()) {
            return Response::deny('No puedes eliminar una organización con usuarios asociados.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotManageTenants($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageTenants($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser, Tenant $tenant): Response
    {
        return $this->cannotManageTenants($currentUser)
            ?? Response::allow();
    }

    public function issueToken(User $currentUser, Tenant $tenant): Response
    {
        return $this->cannotManageTenants($currentUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser, Tenant $tenant): Response
    {
        return $this->cannotManageTenants($currentUser)
            ?? $this->cannotDeleteTenantWithUsers($tenant)
            ?? Response::allow();
    }
}
