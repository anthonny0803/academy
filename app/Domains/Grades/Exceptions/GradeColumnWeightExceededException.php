<?php

namespace App\Domains\Grades\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class GradeColumnWeightExceededException extends RuntimeException implements RenderableDomainException
{
    public static function remaining(float $remainingWeight): self
    {
        return new self("No se puede agregar esta evaluación. Peso restante disponible: {$remainingWeight}%");
    }

    public static function maxAllowed(float $maxAllowedWeight): self
    {
        return new self("El peso total excedería el 100%. Máximo permitido para esta evaluación: {$maxAllowedWeight}%");
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'GRADE_COLUMN_WEIGHT_EXCEEDED';
    }
}
