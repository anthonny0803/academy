<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReassignRepresentativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentRepresentativeId = $this->route('student')->representative_id;

        return [
            'representative_id' => [
                'required',
                'integer',
                'exists:representatives,id',
                Rule::notIn([$currentRepresentativeId]),
            ],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'representative_id' => 'representante',
            'reason' => 'motivo',
        ];
    }

    public function messages(): array
    {
        return [
            'representative_id.required' => 'Debe seleccionar un representante.',
            'representative_id.exists' => 'El representante seleccionado no existe.',
            'representative_id.not_in' => 'El estudiante ya está asignado a este representante.',
            'reason.required' => 'Debe especificar el motivo de la reasignación.',
            'reason.max' => 'El motivo no puede superar los 500 caracteres.',
        ];
    }
}
