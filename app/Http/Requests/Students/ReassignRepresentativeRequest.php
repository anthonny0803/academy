<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\RelationshipType;

class ReassignRepresentativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');

        // Block if student is self-represented
        if ($student->user_id === $student->representative?->user_id) {
            return false;
        }

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
            'relationship_type' => [
                'required',
                'string',
                Rule::in(RelationshipType::toArray()),
                Rule::notIn([RelationshipType::SelfRepresented->value]),
            ],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'representative_id' => 'representante',
            'relationship_type' => 'tipo de relación',
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
