<?php

namespace App\Services\Ai\Contracts;

interface AiProviderInterface
{
    /**
     * Get the provider name.
     */
    public function name(): string;

    /**
     * Check if this provider supports vision tasks.
     */
    public function supportsVision(): bool;

    /**
     * Send a text-based prompt and get response.
     *
     * @param  string  $prompt
     * @param  array   $options  Allowed keys: 'model', 'max_tokens'
     * @return string  Raw response text
     *
     * @throws \App\Services\Ai\Exceptions\AiProviderException
     */
    public function chat(string $prompt, array $options = []): string;

    /**
     * Send an image and prompt for vision-based analysis.
     *
     * @param  string  $base64Image  Base64-encoded image data
     * @param  string  $mimeType     e.g. 'image/jpeg', 'image/png'
     * @param  string  $prompt       The prompt text
     * @param  array   $options      Allowed keys: 'model', 'max_tokens'
     * @return string  Raw response text
     *
     * @throws \App\Services\Ai\Exceptions\AiProviderException
     */
    public function vision(string $base64Image, string $mimeType, string $prompt, array $options = []): string;
}
