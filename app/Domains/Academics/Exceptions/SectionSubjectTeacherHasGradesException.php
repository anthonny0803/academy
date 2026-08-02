<?php

namespace App\Domains\Academics\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class SectionSubjectTeacherHasGradesException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('No se puede eliminar esta asignación porque tiene calificaciones en su historial, incluidas las eliminadas.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'SECTION_SUBJECT_TEACHER_HAS_GRADES';
    }
}
