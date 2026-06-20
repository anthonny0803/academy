<?php

namespace App\Domains\Academics\Http\Requests\Api\Teachers;

use App\Domains\Academics\Http\Requests\Teachers\StoreTeacherRequest as WebStoreTeacherRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreTeacherRequest extends WebStoreTeacherRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
