<?php

namespace App\Http\Requests\Students;

use App\Enums\RelationshipType;
use App\Enums\Sex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->filled('email') ? $this->email : null,
            'document_id' => strtoupper(preg_replace('/[^A-Z0-9]/i', '', $this->document_id ?? '')),
        ]);
    }

    public function rules(): array
    {
        $representativeUserId = $this->input('is_self_represented')
            ? $this->route('representative')->user_id
            : null;

        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($representativeUserId),
            ],
            'sex' => ['required', Rule::in(Sex::toArray())],
            'document_id' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z]{0,1}[0-9]{7,9}[A-Z]{1}$/',
                Rule::unique('users', 'document_id')
                    ->whereNotNull('document_id')
                    ->ignore($representativeUserId),
            ],
            'birth_date' => ['required', 'date', 'before:today'],
            'relationship_type' => ['required', Rule::in(RelationshipType::toArray())],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'is_self_represented' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Validación adicional: verificar que el representante no sea ya un estudiante
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('is_self_represented')) {
                $representative = $this->route('representative');

                if ($representative->user->student) {
                    $validator->errors()->add(
                        'relationship_type',
                        'Este representante ya tiene un perfil de estudiante registrado, puedes inscribirlo directamente en el módulo de estudiantes.'
                    );
                }
            }
        });
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
            'relationship_type' => 'tipo de relación',
            'section_id' => 'sección',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'sex.required' => 'El sexo es obligatorio.',
            'sex.in' => 'El sexo seleccionado no es válido.',
            'document_id.required' => 'El documento de identidad es obligatorio.',
            'document_id.regex' => 'El formato del documento no es válido (ej: 12345678A o X1234567B).',
            'document_id.unique' => 'Este documento ya está registrado.',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'relationship_type.required' => 'El tipo de relación es obligatorio.',
            'relationship_type.in' => 'El tipo de relación seleccionado no es válido.',
            'section_id.required' => 'La sección es obligatoria.',
            'section_id.exists' => 'La sección seleccionada no existe.',
        ];
    }
}