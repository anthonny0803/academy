<?php

namespace App\Domains\Academics\Http\Requests\Api\Teachers;

use App\Domains\Academics\Http\Requests\Teachers\UpdateTeacherRequest as WebUpdateTeacherRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class UpdateTeacherRequest extends WebUpdateTeacherRequest
{
    use ThrowsApiValidationException;
}
