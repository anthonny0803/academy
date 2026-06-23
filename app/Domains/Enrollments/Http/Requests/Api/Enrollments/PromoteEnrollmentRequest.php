<?php

namespace App\Domains\Enrollments\Http\Requests\Api\Enrollments;

use App\Domains\Enrollments\Http\Requests\PromoteEnrollmentRequest as WebPromoteEnrollmentRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class PromoteEnrollmentRequest extends WebPromoteEnrollmentRequest
{
    use ThrowsApiValidationException;
}
