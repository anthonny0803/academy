<?php

namespace App\Http\Requests\SectionSubjectTeacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionSubjectTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_id' => ['required', 'exists:sections,id'],
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('section_subject_teacher')
                    ->where('section_id', $this->section_id)
                    ->where('subject_id', $this->subject_id),
            ],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'is_primary' => ['nullable', 'boolean'],
            'status' => ['required', 'in:activo,suplente,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'La sección es obligatoria.',
            'section_id.exists' => 'La sección seleccionada no existe.',
            'subject_id.required' => 'La materia es obligatoria.',
            'subject_id.exists' => 'La materia seleccionada no existe.',
            'subject_id.unique' => 'Esta materia ya está asignada en esta sección.',
            'teacher_id.required' => 'El profesor es obligatorio.',
            'teacher_id.exists' => 'El profesor seleccionado no existe.',
            'is_primary.boolean' => 'El campo profesor principal debe ser verdadero o falso.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser: activo, suplente o inactivo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'section_id' => 'sección',
            'subject_id' => 'materia',
            'teacher_id' => 'profesor',
            'is_primary' => 'profesor principal',
            'status' => 'estado',
        ];
    }
}