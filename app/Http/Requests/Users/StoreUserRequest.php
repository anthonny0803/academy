<?php

namespace App\Http\Requests\Users;

use App\Enums\Role;
use App\Http\Requests\StorePersonRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends StorePersonRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::enum(Role::class)],
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
