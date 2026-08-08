<?php

namespace App\Domains\Academics\Exceptions;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class AcademicPeriodNotPromotableException extends RuntimeException implements RenderableDomainException
{
    public static function forPeriod(AcademicPeriod $academicPeriod): self
    {
        return new self(
            "El período académico '{$academicPeriod->name}' no admite promociones en su configuración actual."
        );
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'ACADEMIC_PERIOD_NOT_PROMOTABLE';
    }
}
