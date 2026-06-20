<?php

namespace App\Domains\Academics\Http\Requests\Api\Teachers;

use App\Domains\Academics\Http\Requests\Teachers\UpdateTeacherRequest as WebUpdateTeacherRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateTeacherRequest extends WebUpdateTeacherRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
