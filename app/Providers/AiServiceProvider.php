<?php

namespace App\Providers;

use App\Domains\AI\Adapters\AnthropicTextGenerator;
use App\Domains\AI\Contracts\AiTextGenerator;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AiTextGenerator::class, fn (): AnthropicTextGenerator => new AnthropicTextGenerator(
            apiKey: (string) config('ai.anthropic.api_key', ''),
            baseUrl: config('ai.anthropic.base_url'),
            version: config('ai.anthropic.version'),
            model: config('ai.anthropic.model'),
            maxTokens: (int) config('ai.anthropic.max_tokens'),
            timeout: (int) config('ai.anthropic.timeout'),
        ));
    }
}
