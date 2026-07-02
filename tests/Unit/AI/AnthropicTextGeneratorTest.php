<?php

namespace Tests\Unit\AI;

use App\Domains\AI\Adapters\AnthropicTextGenerator;
use App\Domains\AI\Exceptions\AiGenerationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnthropicTextGeneratorTest extends TestCase
{
    private const ENDPOINT = 'https://api.anthropic.com/v1/messages';

    public function test_sends_request_and_returns_the_text_block(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response([
                'content' => [
                    ['type' => 'text', 'text' => 'Hola desde Claude.'],
                ],
            ]),
        ]);

        $text = $this->generator()->generate('Resume esto.', 'Eres un asistente.');

        $this->assertSame('Hola desde Claude.', $text);

        Http::assertSent(fn (Request $request): bool => $request->url() === self::ENDPOINT
            && $request->hasHeader('x-api-key', 'test-key')
            && $request->hasHeader('anthropic-version', '2023-06-01')
            && $request['model'] === 'claude-opus-4-8'
            && $request['max_tokens'] === 1024
            && $request['system'] === 'Eres un asistente.'
            && $request['messages'][0] === ['role' => 'user', 'content' => 'Resume esto.']);
    }

    public function test_omits_the_system_field_when_not_provided(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response([
                'content' => [['type' => 'text', 'text' => 'ok']],
            ]),
        ]);

        $this->generator()->generate('Solo usuario.');

        Http::assertSent(fn (Request $request): bool => ! array_key_exists('system', $request->data()));
    }

    public function test_throws_when_the_provider_responds_with_an_error(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response(['error' => ['message' => 'overloaded']], 529),
        ]);

        $this->expectException(AiGenerationException::class);

        $this->generator()->generate('Hola');
    }

    public function test_throws_when_the_response_has_no_text_block(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response(['content' => []]),
        ]);

        $this->expectException(AiGenerationException::class);

        $this->generator()->generate('Hola');
    }

    public function test_concatenates_every_text_block_and_skips_other_types(): void
    {
        Http::fake([
            self::ENDPOINT => Http::response([
                'content' => [
                    ['type' => 'thinking', 'thinking' => 'razonando...'],
                    ['type' => 'text', 'text' => 'Primera parte. '],
                    ['type' => 'text', 'text' => 'Segunda parte.'],
                ],
            ]),
        ]);

        $text = $this->generator()->generate('Hola');

        $this->assertSame('Primera parte. Segunda parte.', $text);
    }

    public function test_throws_when_the_connection_fails(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection timed out'));

        $this->expectException(AiGenerationException::class);

        $this->generator()->generate('Hola');
    }

    private function generator(): AnthropicTextGenerator
    {
        return new AnthropicTextGenerator(
            apiKey: 'test-key',
            baseUrl: 'https://api.anthropic.com/v1',
            version: '2023-06-01',
            model: 'claude-opus-4-8',
            maxTokens: 1024,
            timeout: 30,
        );
    }
}
