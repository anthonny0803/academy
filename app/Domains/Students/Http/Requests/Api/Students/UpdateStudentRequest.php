<?php

namespace App\Domains\Students\Http\Requests\Api\Students;

use App\Domains\Students\Http\Requests\UpdateStudentRequest as WebUpdateStudentRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateStudentRequest extends WebUpdateStudentRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
