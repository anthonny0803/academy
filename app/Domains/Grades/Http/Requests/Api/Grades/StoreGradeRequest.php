<?php

namespace App\Domains\Grades\Http\Requests\Api\Grades;

use App\Domains\Grades\Http\Requests\Grades\StoreGradeRequest as WebStoreGradeRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreGradeRequest extends WebStoreGradeRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
