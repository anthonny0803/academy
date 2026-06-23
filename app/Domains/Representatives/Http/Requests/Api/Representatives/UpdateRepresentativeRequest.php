<?php

namespace App\Domains\Representatives\Http\Requests\Api\Representatives;

use App\Domains\Representatives\Http\Requests\UpdateRepresentativeRequest as WebUpdateRepresentativeRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class UpdateRepresentativeRequest extends WebUpdateRepresentativeRequest
{
    use ThrowsApiValidationException;
}
