<?php

namespace App\Domains\Academics\Http\Requests\Api\AcademicPeriods;

use App\Domains\Academics\Http\Requests\AcademicPeriods\UpdateAcademicPeriodRequest as WebUpdateAcademicPeriodRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class UpdateAcademicPeriodRequest extends WebUpdateAcademicPeriodRequest
{
    use ThrowsApiValidationException;
}
