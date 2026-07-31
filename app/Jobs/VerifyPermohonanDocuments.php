<?php

namespace App\Jobs;

use App\Models\PermohonanSurat;
use App\Services\Ocr\DocumentOcrService;
use App\Services\PermohonanSuratService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifyPermohonanDocuments implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public array $backoff = [30, 120];
    public int $timeout = 180;

    protected int $permohonanId;

    public function __construct(int $permohonanId)
    {
        $this->permohonanId = $permohonanId;
    }

    public function uniqueId(): string
    {
        return (string) $this->permohonanId;
    }

    public function handle(DocumentOcrService $documentOcr, PermohonanSuratService $permohonanService): void
    {
        $permohonan = PermohonanSurat::with(['jenisSurat', 'dokumens'])->find($this->permohonanId);

        if (!$permohonan) {
            Log::warning("VerifyPermohonanDocuments: permohonan {$this->permohonanId} tidak ditemukan.");
            return;
        }

        if ($permohonan->status !== 'pending') {
            Log::info("VerifyPermohonanDocuments: permohonan {$this->permohonanId} status {$permohonan->status}, skip OCR.");
            return;
        }

        try {
            $result = $documentOcr->verify($permohonan);

            if ($result['status'] === 'not_configured') {
                $permohonan->update(['ocr_status' => 'not_configured']);
                Log::info("VerifyPermohonanDocuments: OCR tidak dikonfigurasi untuk permohonan {$this->permohonanId}.");
                return;
            }

            if ($result['passed']) {
                $permohonan->update([
                    'ocr_status' => 'verified',
                    'ai_insight' => $result['ai_insight'],
                ]);

                // Auto-approve hanya jika enabled di config
                if (config('ai.ocr_auto_approve')) {
                    $permohonanService->autoApprovePermohonan($this->permohonanId, $result['ai_insight']);
                    Log::info("VerifyPermohonanDocuments: Permohonan {$this->permohonanId} lolos OCR → auto-approved.");
                } else {
                    Log::info("VerifyPermohonanDocuments: Permohonan {$this->permohonanId} lolos OCR → verified (auto-approve disabled).");
                }
            } else {
                $permohonan->update([
                    'ocr_status' => 'needs_review',
                    'ai_insight' => $result['ai_insight'],
                ]);

                Log::info("VerifyPermohonanDocuments: Permohonan {$this->permohonanId} tidak lolos OCR → needs_review.");
            }
        } catch (\Exception $e) {
            Log::error("VerifyPermohonanDocuments: Gagal memproses permohonan {$this->permohonanId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            try {
                $permohonan->update([
                    'ocr_status' => 'needs_review',
                    'ai_insight' => [
                        'verdict' => 'error',
                        'ringkasan' => 'Terjadi kesalahan saat verifikasi AI: ' . $e->getMessage(),
                        'detail_ketidakcocokan' => [],
                    ],
                ]);
            } catch (\Exception $updateErr) {
                Log::error("Gagal update ocr_status setelah error: " . $updateErr->getMessage());
            }

            throw $e;
        }
    }
}
