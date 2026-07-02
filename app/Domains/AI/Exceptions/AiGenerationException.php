<?php

namespace App\Domains\AI\Exceptions;

use RuntimeException;

class AiGenerationException extends RuntimeException
{
    public static function requestFailed(int $status, string $providerMessage = ''): self
    {
        return new self(trim("The AI provider request failed with status {$status}. {$providerMessage}"));
    }

    public static function connectionFailed(string $reason): self
    {
        return new self(trim("Could not reach the AI provider. {$reason}"));
    }

    public static function emptyResponse(): self
    {
        return new self('The AI provider returned no text content.');
    }
}
