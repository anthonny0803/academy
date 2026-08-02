<?php

namespace App\Domains\Enrollments\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class EnrollmentHasGradesException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('No se puede eliminar esta inscripción porque tiene calificaciones en su historial, incluidas las eliminadas.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'ENROLLMENT_HAS_GRADES';
    }
}
