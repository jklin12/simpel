<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\Contracts\AiProviderInterface;
use App\Services\Ai\Exceptions\AiProviderException;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AiProviderInterface
{
    public function name(): string
    {
        return 'gemini';
    }

    public function supportsVision(): bool
    {
        return true;
    }

    public function chat(string $prompt, array $options = []): string
    {
        $config = config('ai.providers.gemini');
        $apiKey = $config['api_key'];
        $model = $options['model'] ?? $config['model'];
        $maxTokens = $options['max_tokens'] ?? $config['max_tokens'];
        $timeout = config('ai.timeout');

        if (! $apiKey) {
            throw new AiProviderException('Gemini API key tidak dikonfigurasi.');
        }

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'maxOutputTokens'  => $maxTokens,
                'responseMimeType' => 'application/json',
            ],
        ];

        try {
            $url = $config['base_url'] . '/models/' . $model . ':generateContent?key=' . urlencode($apiKey);

            $response = Http::withHeaders([
                'content-type' => 'application/json',
            ])
                ->timeout($timeout)
                ->post($url, $payload);

            if (! $response->successful()) {
                $errorMsg = $response->json('error.message') ?? 'Kesalahan API Gemini.';
                throw new AiProviderException($errorMsg);
            }

            return $response->json('candidates.0.content.parts.0.text') ?? '';
        } catch (\Exception $e) {
            if ($e instanceof AiProviderException) {
                throw $e;
            }
            throw new AiProviderException('Gemini API error: ' . $e->getMessage());
        }
    }

    public function vision(string $base64Image, string $mimeType, string $prompt, array $options = []): string
    {
        $config = config('ai.providers.gemini');
        $apiKey = $config['api_key'];
        $model = $options['model'] ?? $config['model'];
        $maxTokens = $options['max_tokens'] ?? $config['max_tokens'];
        $timeout = config('ai.timeout');

        if (! $apiKey) {
            throw new AiProviderException('Gemini API key tidak dikonfigurasi.');
        }

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data'      => $base64Image,
                            ],
                        ],
                    ],
                ],
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
            ],
            'generationConfig' => [
                'maxOutputTokens'  => $maxTokens,
                'responseMimeType' => 'application/json',
            ],
        ];

        try {
            $url = $config['base_url'] . '/models/' . $model . ':generateContent?key=' . urlencode($apiKey);

            $response = Http::withHeaders([
                'content-type' => 'application/json',
            ])
                ->timeout($timeout)
                ->post($url, $payload);

            if (! $response->successful()) {
                $errorMsg = $response->json('error.message') ?? 'Kesalahan API Gemini.';
                throw new AiProviderException($errorMsg);
            }

            $text = $response->json('candidates.0.content.parts.0.text') ?? '';
            if ($text === '') {
                $finishReason = $response->json('candidates.0.finishReason') ?? 'unknown';
                \Illuminate\Support\Facades\Log::warning('Gemini vision response empty', [
                    'finishReason' => $finishReason,
                    'prompt'       => substr($prompt, 0, 100),
                ]);
            }
            return $text;
        } catch (\Exception $e) {
            if ($e instanceof AiProviderException) {
                throw $e;
            }
            throw new AiProviderException('Gemini API error: ' . $e->getMessage());
        }
    }
}
