<?php

namespace App\Http\Requests\GradeColumns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradeColumnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sstId = $this->route('sectionSubjectTeacher')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('grade_columns')
                    ->where('section_subject_teacher_id', $sstId),
            ],
            'weight' => [
                'required',
                'numeric',
                'min:0.01',
                'max:100',
            ],
            'display_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'observation' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre de evaluación',
            'weight' => 'ponderación',
            'display_order' => 'orden',
            'observation' => 'observación',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la evaluación es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.unique' => 'Ya existe una evaluación con este nombre en esta asignación.',
            'weight.required' => 'La ponderación es obligatoria.',
            'weight.numeric' => 'La ponderación debe ser un número.',
            'weight.min' => 'La ponderación debe ser mayor a 0.',
            'weight.max' => 'La ponderación no puede superar el 100%.',
            'display_order.integer' => 'El orden debe ser un número entero.',
            'observation.max' => 'La observación no puede superar los 500 caracteres.',
        ];
    }
}