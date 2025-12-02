<?php

namespace App\Http\Requests\Grades;

use App\Models\GradeColumn;
use Illuminate\Foundation\Http\FormRequest;

class BatchGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function getGradeColumn()
    {
        return $this->route('grade_column') ?? $this->route('gradeColumn');
    }

    public function rules(): array
    {
        $gradeColumn = $this->getGradeColumn();
        $sst = $gradeColumn?->sectionSubjectTeacher;
        $academicPeriod = $sst?->section?->academicPeriod;

        $minGrade = $academicPeriod?->min_grade ?? 0;
        $maxGrade = $academicPeriod?->max_grade ?? 100;

        return [
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.enrollment_id' => [
                'required',
                'integer',
                'exists:enrollments,id',
            ],
            'grades.*.value' => [
                'nullable',
                'numeric',
                "min:{$minGrade}",
                "max:{$maxGrade}",
            ],
            'grades.*.observation' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'grades' => 'calificaciones',
            'grades.*.enrollment_id' => 'estudiante',
            'grades.*.value' => 'nota',
            'grades.*.observation' => 'observación',
        ];
    }

    public function messages(): array
    {
        $gradeColumn = $this->getGradeColumn();
        $sst = $gradeColumn?->sectionSubjectTeacher;
        $academicPeriod = $sst?->section?->academicPeriod;

        $minGrade = $academicPeriod?->min_grade ?? 0;
        $maxGrade = $academicPeriod?->max_grade ?? 100;

        return [
            'grades.required' => 'Debe enviar al menos una calificación.',
            'grades.array' => 'El formato de calificaciones no es válido.',
            'grades.*.enrollment_id.required' => 'El estudiante es obligatorio.',
            'grades.*.enrollment_id.exists' => 'El estudiante no existe.',
            'grades.*.value.numeric' => 'La nota debe ser un número.',
            'grades.*.value.min' => "La nota no puede ser menor a {$minGrade}.",
            'grades.*.value.max' => "La nota no puede ser mayor a {$maxGrade}.",
            'grades.*.observation.max' => 'La observación no puede superar los 255 caracteres.',
        ];
    }

    /**
     * Validación adicional después de las reglas básicas
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $gradeColumn = $this->getGradeColumn();
            
            if (!$gradeColumn) {
                $validator->errors()->add('grade_column', 'La columna de evaluación no existe.');
                return;
            }

            $sst = $gradeColumn->sectionSubjectTeacher;

            if (!$sst->isConfigurationComplete()) {
                $remaining = $sst->getRemainingWeight();
                $validator->errors()->add(
                    'configuration',
                    "No puedes calificar hasta completar la configuración. Faltan {$remaining}% para llegar al 100%."
                );
            }
        });
    }
}