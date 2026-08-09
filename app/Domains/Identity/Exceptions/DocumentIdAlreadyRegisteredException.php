<?php

namespace App\Domains\Identity\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class DocumentIdAlreadyRegisteredException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('Otro usuario acaba de registrar ese documento de identidad, vuelve a intentarlo.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'DOCUMENT_ID_ALREADY_REGISTERED';
    }
}
