<?php

namespace App\Domains\Academics\Exceptions;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class AcademicPeriodHasActiveSectionsException extends RuntimeException implements RenderableDomainException
{
    public static function forPeriod(AcademicPeriod $academicPeriod): self
    {
        return new self(
            "No se puede eliminar el período académico '{$academicPeriod->name}' porque tiene secciones activas. Desactívalas primero."
        );
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'ACADEMIC_PERIOD_HAS_ACTIVE_SECTIONS';
    }
}
