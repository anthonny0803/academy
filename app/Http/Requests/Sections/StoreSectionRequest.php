<?php

namespace App\Http\Requests\Sections;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_period_id' => ['required', 'integer', 'exists:academic_periods,id'],
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sections')->where(function ($query) {
                    return $query->where('academic_period_id', $this->academic_period_id);
                }),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'academic_period_id' => 'período académico',
            'name' => 'nombre',
            'description' => 'descripción',
            'capacity' => 'capacidad',
        ];
    }

    public function messages(): array
    {
        return [
            'academic_period_id.required' => 'El período académico es obligatorio.',
            'academic_period_id.exists' => 'El período académico seleccionado no existe.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 50 caracteres.',
            'name.unique' => 'Ya existe una sección con este nombre en el período académico seleccionado.',
            'description.max' => 'La descripción no puede superar los 255 caracteres.',
            'capacity.required' => 'La capacidad es obligatoria.',
            'capacity.integer' => 'La capacidad debe ser un número entero.',
            'capacity.min' => 'La capacidad debe ser al menos 1.',
        ];
    }
}
