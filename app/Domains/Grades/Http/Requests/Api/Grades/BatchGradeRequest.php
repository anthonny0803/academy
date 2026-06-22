<?php

namespace App\Domains\Grades\Http\Requests\Api\Grades;

use App\Domains\Grades\Http\Requests\Grades\BatchGradeRequest as WebBatchGradeRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BatchGradeRequest extends WebBatchGradeRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
