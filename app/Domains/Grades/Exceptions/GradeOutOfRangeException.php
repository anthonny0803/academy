<?php

namespace App\Domains\Grades\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class GradeOutOfRangeException extends RuntimeException implements RenderableDomainException
{
    public static function make(string $minGrade, string $maxGrade): self
    {
        return new self("La nota debe estar entre {$minGrade} y {$maxGrade}.");
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'GRADE_OUT_OF_RANGE';
    }
}
