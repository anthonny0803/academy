<?php

namespace App\Http\Requests\Grades;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $grade = $this->route('grade');
        $academicPeriod = $grade?->gradeColumn?->sectionSubjectTeacher?->section?->academicPeriod;

        $minGrade = $academicPeriod?->min_grade ?? 0;
        $maxGrade = $academicPeriod?->max_grade ?? 100;

        return [
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
            'value' => 'calificación',
            'observation' => 'observación',
        ];
    }

    public function messages(): array
    {
        $grade = $this->route('grade');
        $academicPeriod = $grade?->gradeColumn?->sectionSubjectTeacher?->section?->academicPeriod;

        $minGrade = $academicPeriod?->min_grade ?? 0;
        $maxGrade = $academicPeriod?->max_grade ?? 100;

        return [
            'value.required' => 'La calificación es obligatoria.',
            'value.numeric' => 'La calificación debe ser un número.',
            'value.min' => "La calificación no puede ser menor a {$minGrade}.",
            'value.max' => "La calificación no puede ser mayor a {$maxGrade}.",
            'observation.max' => 'La observación no puede superar los 500 caracteres.',
        ];
    }
}