<?php

namespace App\Domains\Enrollments\Exceptions;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class StudentAlreadyEnrolledInPeriodException extends RuntimeException implements RenderableDomainException
{
    public static function forPeriod(AcademicPeriod $academicPeriod): self
    {
        return new self(
            "Otro usuario acaba de inscribir a este estudiante en el período académico '{$academicPeriod->name}'. Actualiza la página para ver su inscripción vigente."
        );
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'STUDENT_ALREADY_ENROLLED_IN_PERIOD';
    }
}
