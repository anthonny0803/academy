<?php

namespace App\Domains\Academics\Http\Requests\Api\AcademicPeriods;

use App\Domains\Academics\Http\Requests\AcademicPeriods\UpdateAcademicPeriodRequest as WebUpdateAcademicPeriodRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateAcademicPeriodRequest extends WebUpdateAcademicPeriodRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
