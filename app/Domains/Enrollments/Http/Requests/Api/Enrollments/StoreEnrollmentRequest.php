<?php

namespace App\Domains\Enrollments\Http\Requests\Api\Enrollments;

use App\Domains\Enrollments\Http\Requests\StoreEnrollmentRequest as WebStoreEnrollmentRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreEnrollmentRequest extends WebStoreEnrollmentRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
