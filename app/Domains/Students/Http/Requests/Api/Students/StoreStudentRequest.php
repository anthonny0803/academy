<?php

namespace App\Domains\Students\Http\Requests\Api\Students;

use App\Domains\Shared\Traits\ThrowsApiValidationException;
use App\Domains\Students\Http\Requests\StoreStudentRequest as WebStoreStudentRequest;

class StoreStudentRequest extends WebStoreStudentRequest
{
    use ThrowsApiValidationException;
}
