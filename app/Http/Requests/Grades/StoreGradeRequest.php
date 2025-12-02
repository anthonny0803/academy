<?php

namespace App\Http\Requests\Grades;

use App\Models\GradeColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gradeColumn = $this->route('gradeColumn');
        $academicPeriod = $gradeColumn?->sectionSubjectTeacher?->section?->academicPeriod;

        $minGrade = $academicPeriod?->min_grade ?? 0;
        $maxGrade = $academicPeriod?->max_grade ?? 100;

        return [
            'enrollment_id' => [
                'required',
                'integer',
                'exists:enrollments,id',
                Rule::unique('grades')
                    ->where('grade_column_id', $gradeColumn?->id),
            ],
            'value' => [
                'required',
                'numeric',
                "min:{$minGrade}",
                "max:{$maxGrade}",
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
            'enrollment_id' => 'estudiante',
            'value' => 'calificación',
            'observation' => 'observación',
        ];
    }

    public function messages(): array
    {
        $gradeColumn = $this->route('gradeColumn');
        $academicPeriod = $gradeColumn?->sectionSubjectTeacher?->section?->academicPeriod;

        $minGrade = $academicPeriod?->min_grade ?? 0;
        $maxGrade = $academicPeriod?->max_grade ?? 100;

        return [
            'enrollment_id.required' => 'El estudiante es obligatorio.',
            'enrollment_id.exists' => 'El estudiante seleccionado no existe.',
            'enrollment_id.unique' => 'Este estudiante ya tiene una nota en esta evaluación.',
            'value.required' => 'La calificación es obligatoria.',
            'value.numeric' => 'La calificación debe ser un número.',
            'value.min' => "La calificación no puede ser menor a {$minGrade}.",
            'value.max' => "La calificación no puede ser mayor a {$maxGrade}.",
            'observation.max' => 'La observación no puede superar los 500 caracteres.',
        ];
    }
}