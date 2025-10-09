<?php

namespace App\Http\Requests\Users;

use App\Enums\Role;
use App\Http\Requests\Shared\StoreEmployeeRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends StoreEmployeeRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => [
                'required',
                Rule::in(array_map(fn($r) => $r->value, Role::administrativeRoles()))
            ],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);
    }
}
