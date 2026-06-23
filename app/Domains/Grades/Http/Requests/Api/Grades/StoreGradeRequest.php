<?php

namespace App\Domains\Grades\Http\Requests\Api\Grades;

use App\Domains\Grades\Http\Requests\Grades\StoreGradeRequest as WebStoreGradeRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class StoreGradeRequest extends WebStoreGradeRequest
{
    use ThrowsApiValidationException;
}
