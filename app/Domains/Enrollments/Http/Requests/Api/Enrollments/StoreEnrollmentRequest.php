<?php

namespace App\Domains\Enrollments\Http\Requests\Api\Enrollments;

use App\Domains\Enrollments\Http\Requests\StoreEnrollmentRequest as WebStoreEnrollmentRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class StoreEnrollmentRequest extends WebStoreEnrollmentRequest
{
    use ThrowsApiValidationException;
}
