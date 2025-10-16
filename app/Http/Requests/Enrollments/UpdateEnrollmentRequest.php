<?php

namespace App\Http\Requests\Enrollments;

use App\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $enrollment = $this->route('enrollment');

        return [
            'status' => ['required', Rule::in(EnrollmentStatus::toArray())],
            'section_id' => [
                'nullable',
                'integer',
                'exists:sections,id',
                Rule::notIn([$enrollment->section_id]), // No puede ser la misma sección
            ],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'estado',
            'section_id' => 'sección',
            'reason' => 'motivo',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'section_id.exists' => 'La sección seleccionada no existe.',
            'section_id.not_in' => 'La sección debe ser diferente a la actual.',
            'reason.max' => 'El motivo no puede superar los 500 caracteres.',
        ];
    }
}
