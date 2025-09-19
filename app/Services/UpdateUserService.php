<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UpdateUserService
{
    /**
     * Update an existing user with new data and roles.
     *
     * @param User  $user
     * @param array $data
     * @return User
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            // Actualizar la información básica del usuario
            $user->update([
                'name'      => strtoupper($data['name']),
                'last_name' => strtoupper($data['last_name']),
                'email'     => strtolower($data['email']),
                'sex'       => $data['sex'],
            ]);

            // Roles enviados desde el formulario; es un array vacío si no se seleccionó ninguno
            $submittedRoles = $data['roles'] ?? [];

            // Sincronizar roles: asigna los roles seleccionados y remueve los no seleccionados
            $user->syncRoles($submittedRoles);

            // Actualizar el estado de activación
            // Si el usuario tiene al menos un rol, se activa. Si no tiene roles, se desactiva.
            $user->is_active = !empty($submittedRoles);

            $user->save();

            return $user;
        });
    }
}
