<?php

namespace App\Domains\Academics\Http\Requests\Api\Subjects;

use App\Domains\Academics\Http\Requests\Subjects\UpdateSubjectRequest as WebUpdateSubjectRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateSubjectRequest extends WebUpdateSubjectRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
