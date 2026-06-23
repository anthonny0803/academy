<?php

namespace App\Domains\Academics\Http\Requests\Api\Sections;

use App\Domains\Academics\Http\Requests\Sections\UpdateSectionRequest as WebUpdateSectionRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class UpdateSectionRequest extends WebUpdateSectionRequest
{
    use ThrowsApiValidationException;
}
