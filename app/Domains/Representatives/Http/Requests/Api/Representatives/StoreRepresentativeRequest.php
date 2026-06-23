<?php

namespace App\Domains\Representatives\Http\Requests\Api\Representatives;

use App\Domains\Representatives\Http\Requests\StoreRepresentativeRequest as WebStoreRepresentativeRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class StoreRepresentativeRequest extends WebStoreRepresentativeRequest
{
    use ThrowsApiValidationException;
}
