<?php

namespace App\Http\Requests\Teachers;

use App\Http\Requests\Shared\StorePersonRequest;
use Illuminate\Validation\Rules\Password;

class StoreTeacherRequest extends StorePersonRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
    }

    public function messages(): array
    {
        return [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }
}
