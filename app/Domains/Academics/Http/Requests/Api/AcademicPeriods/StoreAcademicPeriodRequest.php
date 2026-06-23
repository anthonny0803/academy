<?php

namespace App\Domains\Academics\Http\Requests\Api\AcademicPeriods;

use App\Domains\Academics\Http\Requests\AcademicPeriods\StoreAcademicPeriodRequest as WebStoreAcademicPeriodRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class StoreAcademicPeriodRequest extends WebStoreAcademicPeriodRequest
{
    use ThrowsApiValidationException;
}
