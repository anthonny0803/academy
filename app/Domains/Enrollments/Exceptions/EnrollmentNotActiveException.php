<?php

namespace App\Domains\Enrollments\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class EnrollmentNotActiveException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('Esta inscripción ya no está activa, así que no admite más movimientos.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'ENROLLMENT_NOT_ACTIVE';
    }
}
