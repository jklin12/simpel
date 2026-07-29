<?php

namespace App\Services\Ai;

use App\Services\Ai\Contracts\AiProviderInterface;
use App\Services\Ai\Exceptions\AiProviderException;
use App\Services\Ai\Providers\ClaudeProvider;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\MockProvider;

class AiManager
{
    protected array $drivers = [];

    public function __construct()
    {
        $this->drivers = [
            'claude' => new ClaudeProvider(),
            'gemini' => new GeminiProvider(),
            'mock'   => new MockProvider(),
        ];
    }

    /**
     * Get a driver instance by name.
     *
     * @param  string|null  $name  Driver name; null = config('ai.provider')
     *
     * @throws \App\Services\Ai\Exceptions\AiProviderException
     */
    public function driver(?string $name = null): AiProviderInterface
    {
        $name = $name ?? config('ai.provider');

        if (! isset($this->drivers[$name])) {
            throw new AiProviderException("AI provider '{$name}' tidak ditemukan.");
        }

        $this->validateProviderConfig($name);

        return $this->drivers[$name];
    }

    /**
     * Get a vision-capable driver instance.
     *
     * @param  string|null  $name  Driver name; null = config('ai.ocr_provider') ?: config('ai.provider')
     *
     * @throws \App\Services\Ai\Exceptions\AiProviderException
     */
    public function visionDriver(?string $name = null): AiProviderInterface
    {
        $name = $name ?? (config('ai.ocr_provider') ?: config('ai.provider'));

        $driver = $this->driver($name);

        if (! $driver->supportsVision()) {
            throw new AiProviderException("Provider '{$name}' tidak mendukung vision tasks.");
        }

        return $driver;
    }

    /**
     * Validate that the provider configuration is valid.
     *
     * @throws \App\Services\Ai\Exceptions\AiProviderException
     */
    protected function validateProviderConfig(string $name): void
    {
        if ($name === 'mock') {
            return;
        }

        $config = config("ai.providers.{$name}");

        if (! $config) {
            throw new AiProviderException("Konfigurasi untuk provider '{$name}' tidak ditemukan.");
        }

        if (! isset($config['api_key']) || ! $config['api_key']) {
            throw new AiProviderException("API key untuk provider '{$name}' tidak dikonfigurasi.");
        }
    }
}
