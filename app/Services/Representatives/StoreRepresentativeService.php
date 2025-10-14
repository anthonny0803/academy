<?php

namespace App\Services\Representatives;

use App\Enums\Role;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreRepresentativeService
{
    public function handle(array $data): Representative
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'sex' => $data['sex'],
                'password' => null,
                'is_active' => false,
            ]);

            $user->assignRole(Role::Representative->value);

            $representative = Representative::create([
                'user_id' => $user->id,
                'document_id' => $data['document_id'],
                'birth_date' => $data['birth_date'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'occupation' => $data['occupation'] ?? null,
                'is_active' => true,
            ]);

            return $representative;
        });
    }
}
