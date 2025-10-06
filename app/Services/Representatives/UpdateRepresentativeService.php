<?php

namespace App\Services;

use App\Models\Representative;
use Illuminate\Support\Facades\DB;

class UpdateRepresentativeService
{
    public function handle(Representative $representative, array $data): array
    {
        return DB::transaction(function () use ($representative, $data) {
            $user = $representative->user;
            $isEmployee = $user->hasRole(['Supervisor', 'Administrador', 'Profesor']);
            $warning = false;
            $sensitiveFields = ['name', 'last_name', 'email', 'sex'];

            if ($isEmployee) {
                foreach ($sensitiveFields as $field) {
                    if (array_key_exists($field, $data)) {
                        $incomingValue = strtoupper($data[$field]);
                        $currentValue  = strtoupper($user->{$field});

                        if ($incomingValue !== $currentValue) {
                            $warning = true;
                            unset($data[$field]);
                        }
                    }
                }
            }

            $user->update([
                'name'      => strtoupper($data['name'] ?? $user->name),
                'last_name' => strtoupper($data['last_name'] ?? $user->last_name),
                'email'     => strtolower($data['email'] ?? $user->email),
                'sex'       => $data['sex'] ?? $user->sex,
            ]);

            $representative->update([
                'document_id' => strtoupper($data['document_id'] ?? $representative->document_id),
                'phone'       => $data['phone'] ?? $representative->phone,
                'occupation'  => strtoupper($data['occupation'] ?? $representative->occupation),
                'birth_date'  => $data['birth_date'] ?? $representative->birth_date,
                'address'     => strtoupper($data['address'] ?? $representative->address),
            ]);

            return [
                'representative' => $representative,
                'warning'        => $warning,
            ];
        });
    }
}
