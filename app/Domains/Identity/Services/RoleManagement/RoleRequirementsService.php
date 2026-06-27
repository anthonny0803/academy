<?php

namespace App\Domains\Identity\Services\RoleManagement;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;

class RoleRequirementsService
{
    public function missingFieldsForRole(User $user, Role $role): array
    {
        $missing = [];

        if (empty($user->password)) {
            $missing[] = 'password';
        }

        if ($role === Role::Representative) {
            $missing = array_merge($missing, $this->missingRepresentativeFields($user));
        }

        return $missing;
    }

    public function roleNeedsForm(User $user, Role $role): bool
    {
        return ! empty($this->missingFieldsForRole($user, $role));
    }

    private function missingRepresentativeFields(User $user): array
    {
        $required = ['document_id', 'birth_date', 'phone', 'address'];

        return array_values(array_filter(
            $required,
            fn (string $field) => empty($user->{$field})
        ));
    }
}
