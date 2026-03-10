<?php

namespace App\Services;

use App\Repositories\Contracts\PermohonanSuratRepositoryInterface;
use App\Models\PermohonanApproval;
use App\Models\SuratCounter;
use App\Models\User;
use App\Notifications\PermohonanApprovedWhatsapp;
use App\Notifications\PermohonanBaruNotification;
use App\Notifications\PermohonanRejectedWhatsapp;
use App\Notifications\PermohonanRevisiNotification;
use App\Notifications\PermohonanRevisiWhatsapp;
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
     * Update data_permohonan secara langsung.
     */
    public function updateDataPermohonan($id, array $data)
    {
        DB::beginTransaction();
        try {
            $permohonan = $this->repository->find($id);

            // Allow update as long as it's not completed or rejected yet
            if (in_array($permohonan->status, ['completed', 'rejected'])) {
                throw new \Exception('Surat sudah selesai atau ditolak, tidak bisa diubah.');
            }

            // Update allowed fields
            $permohonan->update([
                'nama_pemohon'   => $data['nama_pemohon'] ?? $permohonan->nama_pemohon,
                'nik_pemohon'    => $data['nik_pemohon'] ?? $permohonan->nik_pemohon,
                'alamat_pemohon' => $data['alamat_pemohon'] ?? $permohonan->alamat_pemohon,
                'phone_pemohon'  => $data['phone_pemohon'] ?? $permohonan->phone_pemohon,
                'keperluan'      => $data['keperluan'] ?? $permohonan->keperluan,
                'data_permohonan' => array_merge(
                    is_array($permohonan->data_permohonan) ? $permohonan->data_permohonan : [],
                    $data['data_permohonan'] ?? []
                )
            ]);

            DB::commit();
            return $permohonan->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update data_permohonan: ' . $e->getMessage());
            throw new \Exception('Gagal mengubah data permohonan: ' . $e->getMessage());
        }
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

            // Send WhatsApp notification and Draft PDF to Lurah (admin_kelurahan)
            try {
                $lurahAdmins = User::role('lurah')
                    ->where('kelurahan_id', $permohonan->kelurahan_id)
                    ->get();

                foreach ($lurahAdmins as $lurahAdmin) {
                    $lurahAdmin->notify(new PermohonanApprovedWhatsapp($permohonan->fresh()));
                }
            } catch (\Exception $e) {
                Log::error('WA Lurah notification failed: ' . $e->getMessage());
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
     * Revisi permohonan yang sudah ditolak.
     * Approval lama TIDAK dihapus — tetap sebagai riwayat di timeline.
     * Approval baru dibuat dengan catatan "Revisi #N" agar admin tahu ini revisi.
     *
     * @param  \App\Models\PermohonanSurat  $permohonan
     * @param  array  $data  Data baru dari form revisi
     * @param  callable  $fileCallback  fn($permohonan) untuk handle upload file baru
     */
    public function revisiPermohonan($permohonan, array $data, callable $fileCallback)
    {
        if ($permohonan->status !== 'rejected') {
            throw new \Exception('Permohonan ini tidak dalam status ditolak.');
        }

        DB::beginTransaction();
        try {
            // Increment revision count
            $newRevisionCount = $permohonan->revision_count + 1;

            // Update data permohonan
            $permohonan->update([
                'nama_pemohon'    => $data['nama_pemohon']    ?? $permohonan->nama_pemohon,
                'nik_pemohon'     => $data['nik_pemohon']     ?? $permohonan->nik_pemohon,
                'phone_pemohon'   => $data['phone_pemohon']   ?? $permohonan->phone_pemohon,
                'alamat_pemohon'  => $data['alamat_pemohon']  ?? $permohonan->alamat_pemohon,
                'data_permohonan' => array_merge(
                    is_array($permohonan->data_permohonan) ? $permohonan->data_permohonan : [],
                    $data['data_permohonan'] ?? []
                ),
                'status'          => 'pending',
                'current_step'    => 1,
                'revision_count'  => $newRevisionCount,
                'rejected_reason' => null,
            ]);

            // Buat approval baru step 1 dengan catatan revisi
            // Approval lama (rejected) dibiarkan sebagai riwayat
            PermohonanApproval::create([
                'permohonan_surat_id' => $permohonan->id,
                'target_role'         => 'admin_kelurahan',
                'step_name'           => 'Verifikasi Berkas',
                'step_order'          => 1,
                'status'              => 'pending',
                'catatan'             => "Revisi #{$newRevisionCount} — diajukan ulang setelah penolakan",
            ]);

            // Handle upload file baru (jika ada)
            $fileCallback($permohonan);

            DB::commit();

            // 1. Notifikasi push (database) ke admin kelurahan, kecamatan, dan super admin
            try {
                $adminKelurahan = User::role('admin_kelurahan')
                    ->where('kelurahan_id', $permohonan->kelurahan_id)
                    ->get();
                $adminKecamatan = User::role('admin_kecamatan')->get();
                $superAdmins = User::role('super_admin')->get();

                $notifiableAdmins = $adminKelurahan->merge($adminKecamatan)->merge($superAdmins);

                foreach ($notifiableAdmins as $admin) {
                    $admin->notify(new PermohonanRevisiNotification($permohonan->fresh()));
                }
            } catch (\Exception $e) {
                Log::error('Notifikasi revisi admin gagal: ' . $e->getMessage());
            }

            // 2. Notifikasi WhatsApp ke pemohon — konfirmasi revisi berhasil diajukan
            try {
                $permohonan->fresh()->notify(new PermohonanRevisiWhatsapp($permohonan->fresh()));
            } catch (\Exception $e) {
                Log::error('Notifikasi WA revisi pemohon gagal: ' . $e->getMessage());
            }

            Log::info('Permohonan direvisi', [
                'permohonan_id'  => $permohonan->id,
                'revision_count' => $newRevisionCount,
            ]);

            return $permohonan->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal merevisi permohonan: ' . $e->getMessage());
            throw new \Exception('Gagal merevisi permohonan: ' . $e->getMessage());
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

        $kodeJenis    = strtoupper($jenisSurat->kode ?? '');
        $kodeKelurahan = strtoupper($kelurahan->akronim ?? $kelurahan->kode ?? $kelurahan->nama);

        if ($kodeJenis === 'SKM') {
            // Format: 400.12.3.1/002/I/KEL.SN/2026
            $nomorSurat = sprintf(
                '400.12.3.1-SMPL/%03d/%s/%s/%s',
                $counter->counter,
                $this->toRoman($now->month),
                $kodeKelurahan,
                $now->format('Y')
            );
        } elseif ($kodeJenis === 'SKTMR') {
            // Format: 600.2/002/I/LU/2026
            $nomorSurat = sprintf(
                '600.2-SMPL/%03d/%s/%s/%s',
                $counter->counter,
                $this->toRoman($now->month),
                $kodeKelurahan,
                $now->format('Y')
            );
        } elseif ($kodeJenis === 'SKP') {
            // Format: 500/002/I/LU/2026
            $nomorSurat = sprintf(
                '500-SMPL/%03d/%s/%s/%s',
                $counter->counter,
                $this->toRoman($now->month),
                $kodeKelurahan,
                $now->format('Y')
            );
        } elseif ($kodeJenis === 'SKBM') {
            // Format: 400.12/002/I/LU/2026
            $nomorSurat = sprintf(
                '400.12-SMPL/%03d/%s/%s/%s',
                $counter->counter,
                $this->toRoman($now->month),
                $kodeKelurahan,
                $now->format('Y')
            );
        } elseif ($kodeJenis === 'SPN') {
            // Format requested: 472/002/I/KEL.SN/2026
            $nomorSurat = sprintf(
                '472-SMPL/%03d/%s/%s/%s',
                $counter->counter,
                $this->toRoman($now->month),
                $kodeKelurahan,
                $now->format('Y')
            );
        } else {
            // 002/I/KEL.SN/2026
            $nomorSurat = sprintf(
                '%03d/%s/%s/%s',
                $counter->counter,
                $this->toRoman($now->month),
                $kodeKelurahan,
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
