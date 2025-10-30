<?php

namespace App\Http\Requests\RoleManagement;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AssignRoleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Normalizar document_id (quitar guiones y espacios)
        if ($this->filled('document_id')) {
            $this->merge([
                'document_id' => strtoupper(preg_replace('/[^A-Z0-9]/i', '', $this->document_id)),
            ]);
        }

        // Normalizar phone (solo números)
        if ($this->filled('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            ]);
        }

        // Normalizar address (mayúsculas y sin espacios extras)
        if ($this->filled('address')) {
            $this->merge([
                'address' => strtoupper(trim($this->address)),
            ]);
        }

        // Normalizar occupation (mayúsculas y sin espacios extras)
        if ($this->filled('occupation')) {
            $this->merge([
                'occupation' => strtoupper(trim($this->occupation)),
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [];
        
        $targetUser = $this->route('user');
        $role = $this->route('role');

        try {
            $roleEnum = Role::from($role);
        } catch (\ValueError $e) {
            // Si el rol no es válido, no validar nada más
            // (el controller maneja el error)
            return $rules;
        }

        // Validar password si el usuario NO lo tiene
        if (empty($targetUser->password)) {
            $rules['password'] = ['required', 'string', Password::defaults(), 'confirmed'];
        }

        // Validaciones específicas para Representative
        if ($roleEnum === Role::Representative) {
            // document_id - solo validar si NO lo tiene
            if (empty($targetUser->document_id)) {
                $rules['document_id'] = [
                    'required',
                    'string',
                    'regex:/^[A-Z]?[0-9]{7,9}[A-Z]?$/',
                ];
            }

            // birth_date - solo validar si NO lo tiene
            if (empty($targetUser->birth_date)) {
                $rules['birth_date'] = [
                    'required',
                    'date',
                    'before:today',
                ];
            }

            // phone - solo validar si NO lo tiene
            if (empty($targetUser->phone)) {
                $rules['phone'] = [
                    'required',
                    'string',
                    'regex:/^[0-9]{9,15}$/',
                ];
            }

            // address - solo validar si NO lo tiene
            if (empty($targetUser->address)) {
                $rules['address'] = [
                    'required',
                    'string',
                    'max:255',
                ];
            }

            // occupation - siempre nullable
            $rules['occupation'] = ['nullable', 'string', 'max:100'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
            'document_id' => 'documento de identidad',
            'birth_date' => 'fecha de nacimiento',
            'phone' => 'teléfono',
            'address' => 'dirección',
            'occupation' => 'ocupación',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'document_id.required' => 'El documento de identidad es obligatorio.',
            'document_id.regex' => 'El formato del documento de identidad no es válido (ej: 12345678Z o X1234567L).',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El formato del teléfono no es válido (debe tener entre 9 y 15 dígitos).',
            'address.required' => 'La dirección es obligatoria.',
            'address.max' => 'La dirección no puede superar los 255 caracteres.',
            'occupation.max' => 'La ocupación no puede superar los 100 caracteres.',
        ];
    }
}