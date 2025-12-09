<?php

namespace App\Http\Requests\Students;

use App\Enums\StudentSituation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeSituationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'situation' => ['required', Rule::enum(StudentSituation::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'situation' => 'situación',
        ];
    }

    public function messages(): array
    {
        return [
            'situation.required' => 'La situación es obligatoria.',
            'situation.enum' => 'La situación seleccionada no es válida.',
        ];
    }
}