<?php

namespace App\Domains\AI\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class ObservationAlreadyInProgressException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('Ya existe una observación de desempeño en curso para este estudiante.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'OBSERVATION_IN_PROGRESS';
    }
}
