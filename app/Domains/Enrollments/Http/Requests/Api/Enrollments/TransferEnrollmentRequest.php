<?php

namespace App\Domains\Enrollments\Http\Requests\Api\Enrollments;

use App\Domains\Enrollments\Http\Requests\TransferEnrollmentRequest as WebTransferEnrollmentRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class TransferEnrollmentRequest extends WebTransferEnrollmentRequest
{
    use ThrowsApiValidationException;
}
