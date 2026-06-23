<?php

namespace App\Domains\Grades\Http\Requests\Api\Grades;

use App\Domains\Grades\Http\Requests\Grades\BatchGradeRequest as WebBatchGradeRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class BatchGradeRequest extends WebBatchGradeRequest
{
    use ThrowsApiValidationException;
}
