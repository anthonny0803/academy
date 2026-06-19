<?php

namespace App\Domains\Academics\Http\Requests\Api\AcademicPeriods;

use App\Domains\Academics\Http\Requests\AcademicPeriods\StoreAcademicPeriodRequest as WebStoreAcademicPeriodRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreAcademicPeriodRequest extends WebStoreAcademicPeriodRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
