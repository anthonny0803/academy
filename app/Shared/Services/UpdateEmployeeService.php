<?php

namespace App\Shared\Services;

use App\Domains\Identity\Models\User;

class UpdateEmployeeService
{
    public function handle(User $user, array $data): User
    {
        $user->update([
            'email' => $data['email'],
        ]);

        return $user->fresh();
    }
}
