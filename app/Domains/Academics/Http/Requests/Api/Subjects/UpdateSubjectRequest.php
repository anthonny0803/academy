<?php

namespace App\Domains\Academics\Http\Requests\Api\Subjects;

use App\Domains\Academics\Http\Requests\Subjects\UpdateSubjectRequest as WebUpdateSubjectRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class UpdateSubjectRequest extends WebUpdateSubjectRequest
{
    use ThrowsApiValidationException;
}
