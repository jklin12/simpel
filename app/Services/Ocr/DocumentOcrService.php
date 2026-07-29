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
Kamu adalah sistem verifikasi dokumen administrasi kependudukan Indonesia.

DOKUMEN YANG DIPERIKSA: {$label}
{$instruksi}

DATA PEMOHON (dari form input):
{$formContext}

Analisis dokumen ini dan bandingkan dengan data pemohon di atas. Kembalikan HANYA JSON valid (tanpa markdown, tanpa komentar):

{
    "status": "passed" atau "failed",
    "detail": "Penjelasan singkat hasil verifikasi dokumen ini. Jika failed, sebutkan field apa yang tidak cocok.",
    "field_checks": [
        {"field": "Nama", "dokumen": "BUDI SANTOSO", "input": "BUDI SANTOSO", "cocok": true},
        ...
    ]
}

Aturan:
- status = "passed" jika SEMUA data konsisten
- status = "failed" jika ada ketidaksesuaian
- Jika dokumen adalah KTP saksi, cukup pastikan ada informasi 2 orang berbeda
- field_checks: array per-field yang diperiksa
EOT;

            $driver = $this->aiManager->visionDriver();
            $responseText = $driver->vision($imageData['base64'], $imageData['mime'], $prompt);

            $responseText = preg_replace('/```json\s*|\s*```/', '', trim($responseText));
            $parsed = json_decode($responseText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                Log::warning('OCR verify: gagal parse response AI', ['dokumen' => $label, 'raw' => $responseText]);
                return [
                    'dokumen' => $label,
                    'status' => 'failed',
                    'detail' => 'Gagal memproses hasil analisis AI.',
                ];
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
Kamu adalah sistem verifikasi silang dokumen kependudukan.

DATA PEMOHON:
{$formContext}

INSTRUKSI:
{$globalInstruction}

Analisis dan berikan penilaian konsistensi antar dokumen. Kembalikan HANYA JSON:

{
    "status": "passed" atau "failed",
    "detail": "Penjelasan apakah semua data konsisten di seluruh dokumen atau ada yang bertentangan.",
    "field_checks": [
        {"field": "Nama", "dokumen": "semua", "input": "BUDI SANTOSO", "cocok": true}
    ]
}
EOT;

            $driver = $this->aiManager->driver();
            $responseText = $driver->chat($prompt);

            $responseText = preg_replace('/```json\s*|\s*```/', '', trim($responseText));
            $parsed = json_decode($responseText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                return null;
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

        // PDF — konversi halaman pertama ke PNG via Imagick
        if ($mime === 'application/pdf') {
            try {
                if (!class_exists('\Imagick')) {
                    Log::warning('Imagick tidak tersedia, skip PDF: ' . $dokumen->original_name);
                    return null;
                }
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
