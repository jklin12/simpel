<?php

namespace App\Services\Ocr;

use App\Services\Ai\AiManager;
use App\Services\Ai\Exceptions\AiProviderException;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class KtpOcrService
{
    protected AiManager $aiManager;

    public function __construct(AiManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    /**
     * Extract KTP data from an image file.
     *
     * @throws \App\Services\Ai\Exceptions\AiProviderException
     */
    public function extract(UploadedFile $file): array
    {
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        $mimeType = $file->getMimeType();

        $prompt = <<<'EOT'
Kamu adalah sistem OCR KTP Indonesia. Ekstrak data dari foto KTP ini dan kembalikan HANYA JSON valid (tanpa komentar, tanpa markdown) dengan format berikut:
{
  "nik": "string 16 digit atau null",
  "nama": "string nama lengkap atau null",
  "tempat_lahir": "string nama kota atau null",
  "tanggal_lahir": "string format YYYY-MM-DD atau null",
  "jenis_kelamin": "Laki-laki atau Perempuan atau null",
  "alamat": "string alamat lengkap atau null",
  "agama": "string agama atau null",
  "status_perkawinan": "Belum Kawin / Kawin / Cerai Hidup / Cerai Mati atau null",
  "pekerjaan": "string pekerjaan atau null"
}

Aturan penting:
- tanggal_lahir harus dalam format YYYY-MM-DD (misal: 1995-07-25)
- Untuk jenis_kelamin, agama, status_perkawinan, dan pekerjaan usahakan tebak dengan sebaik mungkin jika agak buram.
- Jika ada field yang sama sekali tidak terbaca, isi null
- Kembalikan HANYA JSON, jangan ada teks lain sama sekali
EOT;

        $driver = $this->aiManager->visionDriver();
        $responseText = $driver->vision($imageData, $mimeType, $prompt);

        // Bersihkan markdown block jika ada (penting untuk Claude)
        $responseText = preg_replace('/```json\s*|\s*```/', '', trim($responseText));

        $ktpData = json_decode($responseText, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($ktpData)) {
            throw new AiProviderException('Gagal mengurai respons OCR dari provider.');
        }

        // Extract and normalize the 9 fields
        return [
            'nik'               => $ktpData['nik'] ?? null,
            'nama'              => $ktpData['nama'] ?? null,
            'tempat_lahir'      => $ktpData['tempat_lahir'] ?? null,
            'tanggal_lahir'     => $this->normalizeTanggalLahir($ktpData['tanggal_lahir'] ?? null),
            'jenis_kelamin'     => $this->normalizeJenisKelamin($ktpData['jenis_kelamin'] ?? null),
            'alamat'            => $ktpData['alamat'] ?? null,
            'agama'             => $ktpData['agama'] ?? null,
            'status_perkawinan' => $ktpData['status_perkawinan'] ?? null,
            'pekerjaan'         => $ktpData['pekerjaan'] ?? null,
        ];
    }

    /**
     * Normalize tanggal_lahir to YYYY-MM-DD format.
     */
    protected function normalizeTanggalLahir(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            // Try to parse and re-format to ensure YYYY-MM-DD
            $carbon = Carbon::createFromFormat('Y-m-d', $value);
            return $carbon->format('Y-m-d');
        } catch (\Throwable) {
            // If parsing fails, return as-is (will be the original string)
            return $value;
        }
    }

    /**
     * Normalize jenis_kelamin to standard format.
     */
    protected function normalizeJenisKelamin(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $value = strtoupper(trim($value));

        if (str_contains($value, 'LAKI')) {
            return 'Laki-laki';
        }

        if (str_contains($value, 'PEREM')) {
            return 'Perempuan';
        }

        // Return original if can't determine
        return $value;
    }
}
