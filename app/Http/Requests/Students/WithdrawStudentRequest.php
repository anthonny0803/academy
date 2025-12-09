<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'reason' => 'motivo',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'El motivo del retiro es obligatorio.',
            'reason.max' => 'El motivo no puede superar los 500 caracteres.',
        ];
    }
}