<?php

namespace App\Services;

use App\Models\User;
use App\Models\Representative;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class StoreRepresentativeService
{
    /**
     * Handle the representative creation process within a database transaction.
     *
     * @param array $data
     * @return Representative
     */
    public function handle(array $data): Representative
    {
        return DB::transaction(function () use ($data) {
            // Usa 'firstOrCreate' para encontrar un usuario existente o crear uno nuevo
            // basado en el email.
            $user = User::firstOrCreate(
                ['email' => strtolower($data['email'])],
                [
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'sex' => $data['sex'],
                ]
            );

            // Si el usuario ya existe, actualizamos su nombre y apellido.
            if (!$user->wasRecentlyCreated) {
                $user->update([
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'sex' => $data['sex'],
                ]);
            }

            // Verifica si el usuario ya tiene el rol de representante.
            if ($user->hasRole('Representante')) {
                // Lanza una excepción para que la transacción falle y el catch la capture.
                throw new \Exception('Este usuario ya es un representante.');
            }

            // Asigna el rol 'Representante' al usuario si no lo tiene
            $user->assignRole('Representante');

            // Crea y guarda el registro del representante.
            $representative = Representative::create([
                'user_id' => $user->id,
                'document_id' => strtoupper($data['document_id']),
                'phone' => $data['phone'],
                'occupation' => strtoupper($data['occupation']),
                'address' => strtoupper($data['address']),
                'birth_date' => $data['birth_date'],
                'is_active' => true,
            ]);

            // Dispara el evento de usuario registrado.
            event(new Registered($user));

            // Devuelve el objeto representante para que la variable fuera de la transacción lo capture.
            return $representative;
        });
    }
}
