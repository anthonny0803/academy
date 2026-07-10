<?php

namespace App\Domains\Grades\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class EnrollmentNotGradableException extends RuntimeException implements RenderableDomainException
{
    public static function outsideSection(): self
    {
        return new self('El estudiante no pertenece a esta sección.');
    }

    public static function inactive(): self
    {
        return new self('La inscripción del estudiante no está activa.');
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'ENROLLMENT_NOT_GRADABLE';
    }
}
