<?php

namespace App\Services;

use App\Repositories\Contracts\PermohonanSuratRepositoryInterface;
use App\Models\SuratCounter;
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
                // Final approval - generate letter number
                $nomorSurat = $this->generateNomorSurat($permohonan);

                $this->repository->updateStatus($id, 'completed', [
                    'current_step' => null,
                    'nomor_surat' => $nomorSurat,
                    'tanggal_surat' => now(),
                    'completed_at' => now(),
                ]);
            }

            Log::info('Permohonan approved successfully', [
                'permohonan_id' => $id,
                'approved_by' => $userId,
                'step' => $currentApproval->step_order
            ]);

            DB::commit();

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
     * Generate nomor surat with auto-increment counter
     */
    private function generateNomorSurat($permohonan)
    {
        $jenisSurat = $permohonan->jenisSurat;
        $kelurahan = $permohonan->kelurahan;
        $now = Carbon::now();

        // Get or create counter for this month
        $counter = SuratCounter::firstOrCreate(
            [
                'jenis_surat_id' => $jenisSurat->id,
                'kelurahan_id' => $kelurahan->id,
                'tahun' => $now->year,
                'bulan' => $now->month,
            ],
            ['counter' => 0]
        );

        // Increment counter
        $counter->increment('counter');
        $counter->refresh();

        // Format: 001/SKD/KEL-A/II/2026
        $nomorSurat = sprintf(
            '%03d/%s/%s/%s/%s',
            $counter->counter,
            $jenisSurat->kode,
            $kelurahan->kode,
            $now->format('m'),
            $now->format('Y')
        );

        return $nomorSurat;
    }
}
