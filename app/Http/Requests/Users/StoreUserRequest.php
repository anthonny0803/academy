<?php

namespace App\Http\Requests\Users;

use App\Enums\Role;
use App\Http\Requests\Shared\StoreEmployeeRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends StoreEmployeeRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'role' => [
                'required',
                Rule::in(array_map(fn($r) => $r->value, Role::administrativeRoles()))
            ],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);
    }
}
