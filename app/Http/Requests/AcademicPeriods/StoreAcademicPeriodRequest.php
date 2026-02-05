<?php

namespace App\Http\Requests\AcademicPeriods;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAcademicPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100', Rule::unique('academic_periods')],
            'notes' => ['nullable', 'string', 'max:255'],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before:end_date',
                'before_or_equal:' . now()->addYear()->toDateString(),
            ],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_promotable' => ['nullable', 'boolean'],

            // Campos de escala de calificaciones
            'min_grade' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'passing_grade' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'max_grade' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ];

        // is_transferable only if is_promotable is true
        if ($this->boolean('is_promotable')) {
            $rules['is_transferable'] = ['nullable', 'boolean'];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateGradeScale($validator);
            $this->validateEndDateMax($validator);
        });
    }

    protected function validateEndDateMax($validator): void
    {
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        if (!$startDate || !$endDate) {
            return;
        }

        try {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $maxEnd = $start->copy()->addYears(5);

            if ($end->greaterThan($maxEnd)) {
                $validator->errors()->add(
                    'end_date',
                    'La fecha de fin no puede ser mayor a 5 años desde la fecha de inicio.'
                );
            }
        } catch (\Exception $e) {
            // Si las fechas no son válidas, otras validaciones lo capturarán
        }
    }

    protected function validateGradeScale($validator): void
    {
        $min = $this->input('min_grade');
        $passing = $this->input('passing_grade');
        $max = $this->input('max_grade');

        // Si ninguno está presente, usar defaults de BD
        if ($min === null && $passing === null && $max === null) {
            return;
        }

        // Si alguno está presente, todos deben estar presentes
        if ($min === null || $passing === null || $max === null) {
            $validator->errors()->add('min_grade', 'Si personaliza la escala de calificaciones, debe completar los tres campos.');
            return;
        }

        // Validar relaciones: min < passing <= max
        if ((float) $min >= (float) $passing) {
            $validator->errors()->add('min_grade', 'La nota mínima debe ser menor que la nota de aprobación.');
        }

        if ((float) $passing > (float) $max) {
            $validator->errors()->add('passing_grade', 'La nota de aprobación debe ser menor o igual a la nota máxima.');
        }
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'notes' => 'notas',
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha de fin',
            'is_promotable' => 'permite promoción',
            'is_transferable' => 'permite transferencia',
            'min_grade' => 'nota mínima',
            'passing_grade' => 'nota de aprobación',
            'max_grade' => 'nota máxima',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.unique' => 'Ya existe un período académico con este nombre.',
            'notes.max' => 'Las notas no pueden superar los 255 caracteres.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'start_date.before' => 'La fecha de inicio debe ser anterior a la fecha de fin.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'start_date.before_or_equal' => 'La fecha de inicio no puede ser mayor a 1 año desde hoy.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'min_grade.numeric' => 'La nota mínima debe ser un número.',
            'min_grade.min' => 'La nota mínima no puede ser negativa.',
            'min_grade.max' => 'La nota mínima no puede ser mayor a 1000.',
            'passing_grade.numeric' => 'La nota de aprobación debe ser un número.',
            'passing_grade.min' => 'La nota de aprobación no puede ser negativa.',
            'passing_grade.max' => 'La nota de aprobación no puede ser mayor a 1000.',
            'max_grade.numeric' => 'La nota máxima debe ser un número.',
            'max_grade.min' => 'La nota máxima no puede ser negativa.',
            'max_grade.max' => 'La nota máxima no puede ser mayor a 1000.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('form', 'create')
        );
    }
}