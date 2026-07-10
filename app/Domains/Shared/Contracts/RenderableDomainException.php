<?php

namespace App\Domains\Shared\Contracts;

use Throwable;

interface RenderableDomainException extends Throwable
{
    public function statusCode(): int;

    public function errorCode(): string;
}
