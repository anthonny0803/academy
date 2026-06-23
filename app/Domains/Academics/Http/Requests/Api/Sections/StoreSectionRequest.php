<?php

namespace App\Domains\Academics\Http\Requests\Api\Sections;

use App\Domains\Academics\Http\Requests\Sections\StoreSectionRequest as WebStoreSectionRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class StoreSectionRequest extends WebStoreSectionRequest
{
    use ThrowsApiValidationException;
}
