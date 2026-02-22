<?php

namespace App\Services;

use App\Models\JenisSurat;
use App\Models\PermohonanSurat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Ambil semua data yang dibutuhkan dashboard sesuai role user.
     */
    public function getDashboardData(User $user): array
    {
        $now        = Carbon::now();
        $startMonth = $now->copy()->startOfMonth();
        $startPrev  = $now->copy()->subMonth()->startOfMonth();
        $endPrev    = $now->copy()->subMonth()->endOfMonth();

        // Query base scope berdasarkan role
        $baseQuery = $this->buildBaseQuery($user);

        // ── Stats ──────────────────────────────────────────────────────────
        $permohonanMasukBulanIni  = (clone $baseQuery)->whereBetween('created_at', [$startMonth, $now])->count();
        $permohonanMasukBulanLalu = (clone $baseQuery)->whereBetween('created_at', [$startPrev, $endPrev])->count();

        $menungguVerifikasi = (clone $baseQuery)->where('status', 'pending')->count();

        $selesaiBulanIni  = (clone $baseQuery)->where('status', 'completed')->whereBetween('completed_at', [$startMonth, $now])->count();
        $selesaiBulanLalu = (clone $baseQuery)->where('status', 'completed')->whereBetween('completed_at', [$startPrev, $endPrev])->count();

        $totalJenisSurat = JenisSurat::where('is_active', true)->count();

        // ── Permohonan Terbaru ─────────────────────────────────────────────
        $permohonanTerbaru = (clone $baseQuery)
            ->with(['jenisSurat:id,nama,kode', 'kelurahan:id,nama'])
            ->latest()
            ->limit(8)
            ->get(['id', 'nomor_permohonan', 'nama_pemohon', 'nik_pemohon', 'jenis_surat_id', 'kelurahan_id', 'status', 'created_at']);

        return [
            'stats' => [
                'permohonan_masuk'    => $permohonanMasukBulanIni,
                'permohonan_masuk_pct' => $this->percentageChange($permohonanMasukBulanLalu, $permohonanMasukBulanIni),
                'menunggu_verifikasi' => $menungguVerifikasi,
                'selesai_diproses'    => $selesaiBulanIni,
                'selesai_pct'         => $this->percentageChange($selesaiBulanLalu, $selesaiBulanIni),
                'total_jenis_surat'   => $totalJenisSurat,
            ],
            'permohonan_terbaru' => $permohonanTerbaru,
        ];
    }

    /**
     * Bangun base query sesuai role user.
     */
    private function buildBaseQuery(User $user)
    {
        $query = PermohonanSurat::query();

        if ($user->hasRole('admin_kelurahan')) {
            $query->where('kelurahan_id', $user->kelurahan_id);
        } elseif ($user->hasRole('admin_kecamatan')) {
            $query->whereHas('kelurahan', fn($q) => $q->where('kecamatan_id', $user->kecamatan_id));
        } elseif ($user->hasRole('admin_kabupaten')) {
            $query->whereHas('kelurahan.kecamatan', fn($q) => $q->where('kabupaten_id', $user->kabupaten_id));
        }
        // super_admin: no filter, lihat semua

        return $query;
    }

    /**
     * Hitung persentase perubahan antara dua nilai.
     * Kembalikan array ['value' => int, 'direction' => 'up'|'down'|'same']
     */
    private function percentageChange(int $prev, int $current): array
    {
        if ($prev === 0) {
            return ['value' => $current > 0 ? 100 : 0, 'direction' => $current > 0 ? 'up' : 'same'];
        }

        $pct = round((($current - $prev) / $prev) * 100);

        return [
            'value'     => abs($pct),
            'direction' => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'same'),
        ];
    }
}
