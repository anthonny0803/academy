<?php

namespace App\Domains\AI\Adapters;

use App\Domains\AI\Contracts\AiTextGenerator;
use App\Domains\AI\Exceptions\AiGenerationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AnthropicTextGenerator implements AiTextGenerator
{
    public function __construct(
        private string $apiKey,
        private string $baseUrl,
        private string $version,
        private string $model,
        private int $maxTokens,
        private int $timeout,
    ) {}

    public function generate(string $prompt, ?string $system = null): string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => $this->version,
            ])
                ->timeout($this->timeout)
                ->post("{$this->baseUrl}/messages", $this->payload($prompt, $system));
        } catch (ConnectionException $exception) {
            throw AiGenerationException::connectionFailed($exception->getMessage());
        }

        if ($response->failed()) {
            throw AiGenerationException::requestFailed(
                $response->status(),
                (string) $response->json('error.message', '')
            );
        }

        return $this->extractText($response);
    }

    private function payload(string $prompt, ?string $system): array
    {
        $payload = [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ];

        if ($system !== null) {
            $payload['system'] = $system;
        }

        return $payload;
    }

    private function extractText(Response $response): string
    {
        $texts = [];

        foreach ($response->json('content', []) as $block) {
            if (($block['type'] ?? null) === 'text') {
                $texts[] = (string) $block['text'];
            }
        }

        if ($texts === []) {
            throw AiGenerationException::emptyResponse();
        }

        return implode('', $texts);
    }
}
