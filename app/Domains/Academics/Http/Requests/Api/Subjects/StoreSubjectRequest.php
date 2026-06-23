<?php

namespace App\Domains\Academics\Http\Requests\Api\Subjects;

use App\Domains\Academics\Http\Requests\Subjects\StoreSubjectRequest as WebStoreSubjectRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class StoreSubjectRequest extends WebStoreSubjectRequest
{
    use ThrowsApiValidationException;
}
