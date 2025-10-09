<?php

namespace App\Services\Shared;

use App\Models\User;

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
