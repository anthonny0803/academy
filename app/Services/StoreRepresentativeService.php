<?php

namespace App\Services;

use App\Models\User;
use App\Models\Representative;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class StoreRepresentativeService
{
    public function handle(array $data): Representative
    {
        return DB::transaction(function () use ($data) {
            $user = User::firstOrCreate(
                ['email' => strtolower($data['email'])],
                [
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'sex' => $data['sex'],
                ]
            );

            if (!$user->wasRecentlyCreated) {
                $user->update([
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'sex' => $data['sex'],
                ]);
            }

            if ($user->hasRole('Representante')) {
                throw new \Exception('Este usuario ya es un representante.');
            }

            $user->assignRole('Representante');
            $representative = Representative::create([
                'user_id' => $user->id,
                'document_id' => strtoupper($data['document_id']),
                'phone' => $data['phone'],
                'occupation' => strtoupper($data['occupation']),
                'address' => strtoupper($data['address']),
                'birth_date' => $data['birth_date'],
                'is_active' => true,
            ]);

            return $representative;
        });
    }
}
