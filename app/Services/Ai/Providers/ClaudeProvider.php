<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\Contracts\AiProviderInterface;
use App\Services\Ai\Exceptions\AiProviderException;
use Illuminate\Support\Facades\Http;

class ClaudeProvider implements AiProviderInterface
{
    public function name(): string
    {
        return 'claude';
    }

    public function supportsVision(): bool
    {
        return true;
    }

    public function chat(string $prompt, array $options = []): string
    {
        $config = config('ai.providers.claude');
        $apiKey = $config['api_key'];
        $model = $options['model'] ?? $config['model'];
        $maxTokens = $options['max_tokens'] ?? $config['max_tokens'];
        $timeout = config('ai.timeout');

        if (! $apiKey) {
            throw new AiProviderException('Claude API key tidak dikonfigurasi.');
        }

        $payload = [
            'model'      => $model,
            'max_tokens' => $maxTokens,
            'messages'   => [
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'x-api-key'             => $apiKey,
                'anthropic-version'     => $config['version'],
                'content-type'          => 'application/json',
            ])
                ->timeout($timeout)
                ->post($config['base_url'], $payload);

            if (! $response->successful()) {
                $errorMsg = $response->json('error.message') ?? 'Kesalahan API Claude.';
                throw new AiProviderException($errorMsg);
            }

            return $response->json('content.0.text') ?? '';
        } catch (\Exception $e) {
            if ($e instanceof AiProviderException) {
                throw $e;
            }
            throw new AiProviderException('Claude API error: ' . $e->getMessage());
        }
    }

    public function vision(string $base64Image, string $mimeType, string $prompt, array $options = []): string
    {
        $config = config('ai.providers.claude');
        $apiKey = $config['api_key'];
        $model = $options['model'] ?? $config['model'];
        $maxTokens = $options['max_tokens'] ?? $config['max_tokens'];
        $timeout = config('ai.timeout');

        if (! $apiKey) {
            throw new AiProviderException('Claude API key tidak dikonfigurasi.');
        }

        $payload = [
            'model'      => $model,
            'max_tokens' => $maxTokens,
            'messages'   => [
                [
                    'role'    => 'user',
                    'content' => [
                        [
                            'type'   => 'image',
                            'source' => [
                                'type'       => 'base64',
                                'media_type' => $mimeType,
                                'data'       => $base64Image,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'x-api-key'             => $apiKey,
                'anthropic-version'     => $config['version'],
                'content-type'          => 'application/json',
            ])
                ->timeout($timeout)
                ->post($config['base_url'], $payload);

            if (! $response->successful()) {
                $errorMsg = $response->json('error.message') ?? 'Kesalahan API Claude.';
                throw new AiProviderException($errorMsg);
            }

            return $response->json('content.0.text') ?? '';
        } catch (\Exception $e) {
            if ($e instanceof AiProviderException) {
                throw $e;
            }
            throw new AiProviderException('Claude API error: ' . $e->getMessage());
        }
    }
}
