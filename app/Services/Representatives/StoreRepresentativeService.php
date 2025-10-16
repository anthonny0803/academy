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
                'password' => $data['password'],
                'sex' => $data['sex'],
                'document_id' => $data['document_id'],
                'birth_date' => $data['birth_date'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'occupation' => $data['occupation'],
                'is_active' => true,
            ]);

            $user->assignRole(Role::Representative->value);

            $representative = Representative::create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);

            return $representative->fresh(['user']);
        });
    }
}
