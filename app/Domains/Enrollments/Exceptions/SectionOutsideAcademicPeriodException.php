<?php

namespace App\Domains\Enrollments\Exceptions;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class SectionOutsideAcademicPeriodException extends RuntimeException implements RenderableDomainException
{
    public static function forSection(Section $section, AcademicPeriod $academicPeriod): self
    {
        return new self(
            "La sección '{$section->name}' no pertenece al período académico '{$academicPeriod->name}', así que no es un destino válido para esta promoción."
        );
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'SECTION_OUTSIDE_ACADEMIC_PERIOD';
    }
}
