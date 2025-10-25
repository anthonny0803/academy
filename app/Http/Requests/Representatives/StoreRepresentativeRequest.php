<?php

namespace App\Http\Requests\Representatives;

use App\Enums\Sex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRepresentativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->document_id) {
            $this->merge([
                'document_id' => strtoupper(preg_replace('/[^A-Z0-9]/i', '', $this->document_id)),
            ]);
        }

        if ($this->phone) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')],
            'sex' => ['required', Rule::enum(Sex::class)],
            'document_id' => [
                'required',
                'string',
                'regex:/^[A-Z]?[0-9]{7,9}[A-Z]?$/',
                Rule::unique('users', 'document_id'),
            ],
            'birth_date' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{9,15}$/'],
            'address' => ['required', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Este correo ya está registrado en el sistema.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'sex.enum' => 'El sexo debe ser Masculino o Femenino.',
            'document_id.regex' => 'El documento debe tener formato válido (Ej: 12345678A, X1234567B).',
            'document_id.unique' => 'Este documento ya está registrado.',
            'birth_date.date' => 'La fecha de nacimiento no es válida.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'birth_date.after' => 'La fecha de nacimiento debe ser posterior a 1900.',
            'phone.regex' => 'El teléfono debe tener entre 9 y 15 dígitos.',
            'address.max' => 'La dirección no puede exceder 255 caracteres.',
            'occupation.max' => 'La ocupación no puede exceder 100 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'last_name' => 'apellido',
            'email' => 'correo electrónico',
            'sex' => 'sexo',
            'document_id' => 'documento de identidad',
            'birth_date' => 'fecha de nacimiento',
            'phone' => 'teléfono',
            'address' => 'dirección',
            'occupation' => 'ocupación',
        ];
    }
}
