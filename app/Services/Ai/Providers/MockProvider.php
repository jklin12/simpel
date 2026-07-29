<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\Contracts\AiProviderInterface;
use Illuminate\Support\Str;

class MockProvider implements AiProviderInterface
{
    public function name(): string
    {
        return 'mock';
    }

    public function supportsVision(): bool
    {
        return true;
    }

    public function chat(string $prompt, array $options = []): string
    {
        return json_encode([
            'mock' => true,
            'message' => 'Mock chat response',
        ]);
    }

    public function vision(string $base64Image, string $mimeType, string $prompt, array $options = []): string
    {
        // Detect if this is a KTP extraction prompt or a document verification prompt
        if (str_contains($prompt, '"status": "passed"') || str_contains($prompt, '"field_checks"')) {
            // Document verification prompt
            return json_encode([
                'status' => 'passed',
                'detail' => 'Mock: Semua data konsisten dengan dokumen.',
                'field_checks' => [
                    ['field' => 'Nama', 'dokumen' => 'MOCK USER', 'input' => 'MOCK USER', 'cocok' => true],
                    ['field' => 'NIK', 'dokumen' => '6372010000000001', 'input' => '6372010000000001', 'cocok' => true],
                ],
            ]);
        }

        // Return mock KTP data as JSON string
        return json_encode([
            'nik' => '637201' . rand(1000000000, 9999999999),
            'nama' => 'MOCK ' . Str::random(10),
            'tempat_lahir' => 'BANJARBARU',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'JL. RAYA PUSAKA NO. 123, RT 01 RW 02, KEL. GUNTUNG P., KEC. BANJARBARU',
            'agama' => 'ISLAM',
            'status_perkawinan' => 'BELUM KAWIN',
            'pekerjaan' => 'KARYAWAN SWASTA',
        ]);
    }
}
