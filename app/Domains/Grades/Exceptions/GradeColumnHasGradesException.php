<?php

namespace App\Domains\Grades\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class GradeColumnHasGradesException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('No se puede eliminar esta evaluación porque tiene notas en su historial, incluidas las eliminadas.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'GRADE_COLUMN_HAS_GRADES';
    }
}
