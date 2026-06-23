<?php

namespace App\Domains\Students\Http\Requests\Api\Students;

use App\Domains\Shared\Traits\ThrowsApiValidationException;
use App\Domains\Students\Http\Requests\UpdateStudentRequest as WebUpdateStudentRequest;

class UpdateStudentRequest extends WebUpdateStudentRequest
{
    use ThrowsApiValidationException;
}
