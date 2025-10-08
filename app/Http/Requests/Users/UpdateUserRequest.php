<?php

namespace App\Http\Requests\Users;

use App\Enums\Role;
use App\Http\Requests\Shared\UpdatePersonRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends UpdatePersonRequest
{
    protected function getUserId(): int
    {
        return $this->route('user')->id;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'role' => [
                'required',
                Rule::in(array_map(fn($r) => $r->value, Role::administrativeRoles())),
            ],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',
        ]);
    }
}
