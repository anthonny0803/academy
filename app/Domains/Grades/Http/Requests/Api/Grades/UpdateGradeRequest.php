<?php

namespace App\Domains\Grades\Http\Requests\Api\Grades;

use App\Domains\Grades\Http\Requests\Grades\UpdateGradeRequest as WebUpdateGradeRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class UpdateGradeRequest extends WebUpdateGradeRequest
{
    use ThrowsApiValidationException;
}
