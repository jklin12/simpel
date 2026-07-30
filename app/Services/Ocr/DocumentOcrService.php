<?php

namespace App\Services\Ocr;

use App\Models\PermohonanSurat;
use App\Models\PermohonanDokumen;
use App\Services\Ai\AiManager;
use App\Services\Ai\Exceptions\AiProviderException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentOcrService
{
    protected AiManager $aiManager;

    public function __construct(AiManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    /**
     * Verify a permohonan's documents against its form data.
     *
     * @return array ['status' => 'verified'|'needs_review'|'not_configured', 'passed' => bool, 'ai_insight' => array, 'details' => array]
     */
    public function verify(PermohonanSurat $permohonan): array
    {
        // Check if OCR is globally enabled
        if (!config('ai.ocr_enabled')) {
            return [
                'status' => 'not_configured',
                'passed' => true,
                'ai_insight' => null,
                'details' => [],
            ];
        }

        $jenisSurat = $permohonan->jenisSurat;
        $ocrRules = $jenisSurat->ocr_rules ?? null;

        if (!$ocrRules || empty($ocrRules['dokumen'])) {
            return [
                'status' => 'not_configured',
                'passed' => true,
                'ai_insight' => null,
                'details' => [],
            ];
        }

        $formData = $permohonan->data_permohonan ?? [];
        $dokumens = $permohonan->dokumens->keyBy('jenis_dokumen');

        $perDokumen = [];
        $allPassed = true;
        $hasMissingWajib = false;

        foreach ($ocrRules['dokumen'] as $rule) {
            $jenisDokumen = $rule['jenis_dokumen'] ?? null;
            $wajib = $rule['wajib'] ?? false;
            $label = $rule['label'] ?? $jenisDokumen;
            $instruksi = $rule['instruksi'] ?? '';

            $dokumen = $dokumens->get($jenisDokumen);

            if (!$dokumen) {
                if ($wajib) {
                    $allPassed = false;
                    $hasMissingWajib = true;
                    $perDokumen[] = [
                        'dokumen' => $label,
                        'status' => 'failed',
                        'detail' => "Dokumen '$label' tidak ditemukan (wajib diupload).",
                    ];
                }
                continue;
            }

            $result = $this->verifySingleDocument($dokumen, $formData, $label, $instruksi);
            $perDokumen[] = $result;
            if ($result['status'] !== 'passed') {
                $allPassed = false;
            }
        }

        // Cross-check global antar dokumen
        $crossCheckResult = $this->crossCheckDocuments($dokumens, $formData, $ocrRules['instruksi_global'] ?? '');
        if ($crossCheckResult && $crossCheckResult['status'] !== 'passed') {
            $allPassed = false;
            $perDokumen[] = $crossCheckResult;
        }

        $aiInsight = $this->buildAiInsight($allPassed, $perDokumen, $formData);

        return [
            'status' => $allPassed ? 'verified' : 'needs_review',
            'passed' => $allPassed,
            'ai_insight' => $aiInsight,
            'details' => $perDokumen,
        ];
    }

    protected function verifySingleDocument(PermohonanDokumen $dokumen, array $formData, string $label, string $instruksi): array
    {
        try {
            $imageData = $this->readDocumentAsBase64($dokumen);
            if (!$imageData) {
                return [
                    'dokumen' => $label,
                    'status' => 'failed',
                    'detail' => "Gagal membaca file {$dokumen->original_name} (format tidak didukung).",
                ];
            }

            $formContext = $this->formatFormData($formData);

            $prompt = <<<EOT
TASK: Verifikasi dokumen administrasi Indonesia

DOKUMEN: {$label}
{$instruksi}

DATA FORM: {$formContext}

RESPONS HARUS BERUPA JSON VALID YANG SEMPURNA. JANGAN TAMBAH KOMENTAR, MARKDOWN, ATAU TEKS LAIN.

Struktur JSON (COPY PERSIS, GANTI NILAI):
{"status":"passed","detail":"Keterangan verifikasi max 150 karakter.","field_checks":[]}

PEDOMAN:
1. status = "passed" jika semua data cocok, atau "failed" jika ada perbedaan
2. detail = penjelasan singkat perbedaan (jika ada)
3. field_checks = kosong array [] saja

MULAI RESPONS DENGAN { DAN AKHIRI DENGAN }
EOT;

            $driver = $this->aiManager->visionDriver();
            $responseText = $driver->vision($imageData['base64'], $imageData['mime'], $prompt);

            // Clean response: remove markdown, trim, extract JSON
            $responseText = trim($responseText);
            $responseText = preg_replace('/```json\s*|\s*```|```\s*/', '', $responseText);
            $responseText = trim($responseText);

            // Try to extract JSON if response contains extra text
            if (strpos($responseText, '{') !== false) {
                $jsonStart = strpos($responseText, '{');
                $jsonEnd = strrpos($responseText, '}');
                if ($jsonEnd !== false && $jsonEnd > $jsonStart) {
                    $responseText = substr($responseText, $jsonStart, $jsonEnd - $jsonStart + 1);
                }
            }

            // Remove control characters and fix encoding issues
            $responseText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', ' ', $responseText);
            $responseText = preg_replace('/\s+/', ' ', $responseText); // collapse multiple spaces

            $parsed = json_decode($responseText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                // Try more aggressive cleaning
                $responseText = preg_replace('/\x00|[\x00-\x1F\x7F-\xFF]/', '', $responseText);
                $parsed = json_decode($responseText, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                    // Fallback: extract status from incomplete JSON if possible
                    if (preg_match('/"status"\s*:\s*"(passed|failed)"/', $responseText, $matches)) {
                        Log::info('OCR verify: partial JSON fallback', [
                            'dokumen' => $label,
                            'status' => $matches[1],
                        ]);
                        return [
                            'dokumen' => $label,
                            'status' => $matches[1],
                            'detail' => 'Respons AI incomplete - gunakan status dari fallback.',
                            'field_checks' => [],
                        ];
                    }

                    Log::warning('OCR verify: gagal parse response AI', [
                        'dokumen' => $label,
                        'raw_text' => substr($responseText, 0, 300),
                        'json_error' => json_last_error_msg(),
                        'length' => strlen($responseText),
                    ]);
                    return [
                        'dokumen' => $label,
                        'status' => 'failed',
                        'detail' => 'Gagal memproses hasil analisis AI.',
                    ];
                }
            }

            $verdict = $parsed['status'] ?? 'failed';
            if (!in_array($verdict, ['passed', 'failed'])) {
                $verdict = 'failed';
            }

            return [
                'dokumen' => $label,
                'status' => $verdict,
                'detail' => $parsed['detail'] ?? '',
                'field_checks' => $parsed['field_checks'] ?? [],
            ];
        } catch (AiProviderException $e) {
            Log::error("OCR AI error for {$label}: " . $e->getMessage());
            return [
                'dokumen' => $label,
                'status' => 'failed',
                'detail' => 'Gagal memproses OCR: ' . $e->getMessage(),
            ];
        }
    }

    protected function crossCheckDocuments($dokumens, array $formData, string $globalInstruction): ?array
    {
        if (empty($globalInstruction) || $dokumens->count() < 2) {
            return null;
        }

        try {
            $formContext = $this->formatFormData($formData);

            $prompt = <<<EOT
TASK: Cross-check konsistensi dokumen kependudukan

DATA FORM: {$formContext}

INSTRUKSI: {$globalInstruction}

RESPONS HANYA JSON VALID. JANGAN TAMBAH TEKS LAIN.

JSON (COPY PERSIS, GANTI NILAI):
{"status":"passed","detail":"Deskripsi konsistensi max 150 karakter.","field_checks":[]}

PEDOMAN:
1. status = "passed" jika semua konsisten, "failed" jika kontradiksi
2. detail = ringkasan singkat
3. field_checks = array kosong []

MULAI DENGAN { AKHIRI DENGAN }
EOT;

            $driver = $this->aiManager->driver();
            $responseText = $driver->chat($prompt);

            // Clean response: remove markdown, trim, extract JSON
            $responseText = trim($responseText);
            $responseText = preg_replace('/```json\s*|\s*```|```\s*/', '', $responseText);
            $responseText = trim($responseText);

            // Try to extract JSON if response contains extra text
            if (strpos($responseText, '{') !== false) {
                $jsonStart = strpos($responseText, '{');
                $jsonEnd = strrpos($responseText, '}');
                if ($jsonEnd !== false && $jsonEnd > $jsonStart) {
                    $responseText = substr($responseText, $jsonStart, $jsonEnd - $jsonStart + 1);
                }
            }

            // Remove control characters and fix encoding issues
            $responseText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', ' ', $responseText);
            $responseText = preg_replace('/\s+/', ' ', $responseText); // collapse multiple spaces

            $parsed = json_decode($responseText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                // Try more aggressive cleaning
                $responseText = preg_replace('/\x00|[\x00-\x1F\x7F-\xFF]/', '', $responseText);
                $parsed = json_decode($responseText, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                    Log::warning('Cross-check: gagal parse response AI', [
                        'raw_text' => substr($responseText, 0, 300),
                        'json_error' => json_last_error_msg(),
                        'length' => strlen($responseText),
                    ]);
                    return null;
                }
            }

            return [
                'dokumen' => 'Cross-check Semua Dokumen',
                'status' => $parsed['status'] ?? 'failed',
                'detail' => $parsed['detail'] ?? '',
                'field_checks' => $parsed['field_checks'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::warning('Cross-check dokumen gagal: ' . $e->getMessage());
            return null;
        }
    }

    protected function readDocumentAsBase64(PermohonanDokumen $dokumen): ?array
    {
        $fullPath = Storage::disk('local')->path($dokumen->file_path);
        $mime = $dokumen->mime_type ?? '';

        if (!file_exists($fullPath)) {
            return null;
        }

        // Image — langsung base64
        if (str_starts_with($mime, 'image/')) {
            return [
                'base64' => base64_encode(file_get_contents($fullPath)),
                'mime' => $mime,
            ];
        }

        // PDF — skip jika Imagick/GhostScript not available
        if ($mime === 'application/pdf') {
            if (!extension_loaded('imagick')) {
                Log::warning('PDF skip: Imagick not loaded: ' . $dokumen->original_name);
                return null;
            }

            try {
                $imagick = new \Imagick($fullPath . '[0]');
                $imagick->setImageFormat('png');
                $blob = $imagick->getImageBlob();
                $imagick->clear();

                return [
                    'base64' => base64_encode($blob),
                    'mime' => 'image/png',
                ];
            } catch (\Exception $e) {
                Log::warning('Gagal konversi PDF ke PNG: ' . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    protected function formatFormData(array $formData): string
    {
        if (empty($formData)) {
            return '(tidak ada data)';
        }

        $lines = [];
        foreach ($formData as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $label = ucwords(str_replace('_', ' ', $key));
            $lines[] = "- {$label}: {$value}";
        }
        return implode("\n", $lines);
    }

    protected function buildAiInsight(bool $allPassed, array $perDokumen, array $formData): array
    {
        $passedCount = 0;
        $failedCount = 0;
        $failedDetails = [];

        foreach ($perDokumen as $result) {
            if ($result['status'] === 'passed') {
                $passedCount++;
            } else {
                $failedCount++;
                $failedDetails[] = $result['detail'];
            }
        }

        $ringkasan = $allPassed
            ? "✅ Verifikasi AI: Semua {$passedCount} dokumen terverifikasi. Data konsisten."
            : "⚠️ Verifikasi AI: {$passedCount} dokumen OK, {$failedCount} dokumen bermasalah.";

        return [
            'verdict' => $allPassed ? 'passed' : 'failed',
            'ringkasan' => $ringkasan,
            'detail_ketidakcocokan' => $failedDetails,
            'total_dokumen_diperiksa' => count($perDokumen),
            'total_passed' => $passedCount,
            'total_failed' => $failedCount,
        ];
    }
}
