<?php

namespace App\Domains\Students\Http\Requests\Api\Students;

use App\Domains\Students\Http\Requests\StoreStudentRequest as WebStoreStudentRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreStudentRequest extends WebStoreStudentRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
