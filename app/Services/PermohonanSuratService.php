<?php

namespace App\Services;

use App\Repositories\Contracts\PermohonanSuratRepositoryInterface;
use App\Models\SuratCounter;
use App\Notifications\PermohonanApprovedWhatsapp;
use App\Notifications\PermohonanRejectedWhatsapp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PermohonanSuratService
{
    protected $repository;

    public function __construct(PermohonanSuratRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPermohonan()
    {
        return $this->repository->all();
    }

    public function getPermohonanPaginated($perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getPermohonanByUserRole($user, array $filters = [], $perPage = 15)
    {
        return $this->repository->getByUserRole($user, $filters, $perPage);
    }

    public function getPermohonanById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Approve permohonan at current step
     */
    public function approvePermohonan($id, $userId, $catatan = null)
    {
        DB::beginTransaction();

        try {
            $permohonan = $this->repository->find($id);
            $user = Auth::user();

            // Get current pending approval
            $currentApproval = $this->repository->getCurrentApproval($id, $permohonan->current_step);

            if (!$currentApproval) {
                throw new \Exception('Tidak ada approval yang pending untuk permohonan ini');
            }

            // Check if user has the right role
            if (!$user->hasRole($currentApproval->target_role)) {
                throw new \Exception('Anda tidak memiliki wewenang untuk approve di step ini');
            }

            // Update approval
            $currentApproval->update([
                'status' => 'approved',
                'user_id' => $userId,
                'catatan' => $catatan,
                'approved_at' => now(),
            ]);

            // Check if there are more steps
            $nextApproval = $this->repository->getNextApproval($id, $currentApproval->step_order);

            if ($nextApproval) {
                // Move to next step
                $this->repository->updateStatus($id, 'in_review', [
                    'current_step' => $nextApproval->step_order,
                ]);
            } else {
                // Final approval - generate letter number, status -> approved (pending TTD upload)
                $nomorSurat = $this->generateNomorSurat($permohonan);

                $this->repository->updateStatus($id, 'approved', [
                    'current_step' => null,
                    'nomor_surat' => $nomorSurat,
                    'tanggal_surat' => now(),
                ]);
            }

            Log::info('Permohonan approved successfully', [
                'permohonan_id' => $id,
                'approved_by' => $userId,
                'step' => $currentApproval->step_order
            ]);

            DB::commit();

            // Send WhatsApp notification to applicant
            try {
                $permohonan->fresh()->notify(new PermohonanApprovedWhatsapp($permohonan->fresh()));
            } catch (\Exception $e) {
                Log::error('WA Approved notification failed: ' . $e->getMessage());
            }

            return $permohonan->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve permohonan', [
                'permohonan_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal approve permohonan: ' . $e->getMessage());
        }
    }

    /**
     * Reject permohonan
     */
    public function rejectPermohonan($id, $userId, $rejectedReason)
    {
        DB::beginTransaction();

        try {
            $permohonan = $this->repository->find($id);
            $user = Auth::user();

            // Get current pending approval
            $currentApproval = $this->repository->getCurrentApproval($id, $permohonan->current_step);

            if (!$currentApproval) {
                throw new \Exception('Tidak ada approval yang pending untuk permohonan ini');
            }

            // Check if user has the right role
            if (!$user->hasRole($currentApproval->target_role)) {
                throw new \Exception('Anda tidak memiliki wewenang untuk reject di step ini');
            }

            // Update approval
            $currentApproval->update([
                'status' => 'rejected',
                'user_id' => $userId,
                'catatan' => $rejectedReason,
                'approved_at' => now(),
            ]);

            // Update permohonan
            $this->repository->updateStatus($id, 'rejected', [
                'rejected_reason' => $rejectedReason,
            ]);

            Log::info('Permohonan rejected successfully', [
                'permohonan_id' => $id,
                'rejected_by' => $userId
            ]);

            DB::commit();

            // Send WhatsApp notification to applicant
            try {
                $permohonan->fresh()->notify(new PermohonanRejectedWhatsapp($permohonan->fresh(), $rejectedReason));
            } catch (\Exception $e) {
                Log::error('WA Rejected notification failed: ' . $e->getMessage());
            }

            return $permohonan->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject permohonan', [
                'permohonan_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal reject permohonan: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor surat with auto-increment counter.
     * Format per jenis surat:
     *   - SKTMR : 600.2/{counter}/{RomawiBulan}/{kode_kelurahan}/{tahun}
     *   - Others: {counter}/{kode_surat}/{kode_kelurahan}/{bulan}/{tahun}
     */
    private function generateNomorSurat($permohonan)
    {
        $jenisSurat = $permohonan->jenisSurat;
        $kelurahan  = $permohonan->kelurahan;
        $now        = Carbon::now();

        // Get or create counter for this month
        $counter = SuratCounter::firstOrCreate(
            [
                'jenis_surat_id' => $jenisSurat->id,
                'kelurahan_id'   => $kelurahan->id,
                'tahun'          => $now->year,
                'bulan'          => $now->month,
            ],
            ['counter' => 0]
        );

        $counter->increment('counter');
        $counter->refresh();

        $kodeJenis = strtoupper($jenisSurat->kode ?? '');

        if ($kodeJenis === 'SKTMR') {
            // Format: 600.2/002/I/KEL.SN/2026
            $nomorSurat = sprintf(
                '600.2/%03d/%s/%s/%s',
                $counter->counter,
                $this->toRoman($now->month),
                $kelurahan->kode ?? strtoupper($kelurahan->nama),
                $now->format('Y')
            );
        } else {
            // Format default: 001/SKD/KEL-A/II/2026
            $nomorSurat = sprintf(
                '%03d/%s/%s/%s/%s',
                $counter->counter,
                $kodeJenis,
                $kelurahan->kode ?? strtoupper($kelurahan->nama),
                $this->toRoman($now->month),
                $now->format('Y')
            );
        }

        return $nomorSurat;
    }

    /**
     * Convert integer month (1-12) to Roman numeral string.
     */
    private function toRoman(int $month): string
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $map[$month] ?? (string)$month;
    }
}
