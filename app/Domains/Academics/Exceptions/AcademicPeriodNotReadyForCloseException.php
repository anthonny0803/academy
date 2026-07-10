<?php

namespace App\Domains\Academics\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class AcademicPeriodNotReadyForCloseException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('No se puede cerrar el período. Hay inscripciones con datos incompletos.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'ACADEMIC_PERIOD_NOT_READY_FOR_CLOSE';
    }
}
