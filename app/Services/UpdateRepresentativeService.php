<?php

namespace App\Services;

use App\Models\Representative;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateRepresentativeService
{
    /**
     * Update an existing representative with new data.
     *
     * @param Representative  $representative
     * @param array $data
     * @return Representative
     */
    public function handle(Representative $representative, array $data): Representative
    {
        return DB::transaction(function () use ($representative, $data) {

            // Actualizamos primero la tabla users
            if ($representative->user) {
                $representative->user->update([
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'email' => strtolower($data['email']),
                    'sex' => $data['sex'],
                ]);
            }

            // Luego actualizamos la tabla representatives si hay campos específicos de ella
            $representative->update([
                'document_id' => strtoupper($data['document_id'] ?? $representative->document_id),
                'phone'       => $data['phone'] ?? $representative->phone,
                'occupation'  => strtoupper($data['occupation'] ?? $representative->occupation),
                'birth_date'  => $data['birth_date'] ?? $representative->birth_date,
                'address'     => strtoupper($data['address'] ?? $representative->address),
            ]);

            return $representative;
        });
    }
}
