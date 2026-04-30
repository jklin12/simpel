<?php

namespace App\Services;

use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\PermohonanSurat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardService
{
    /**
     * Default SLA target (jam) untuk hitung % within target.
     */
    private const SLA_TARGET_HOURS = 48;

    /**
     * Ambil semua data yang dibutuhkan dashboard sesuai role user.
     * Filter opsional: $month (1-12), $year (YYYY)
     */
    public function getDashboardData(User $user, ?int $month = null, ?int $year = null): array
    {
        // Tentukan periode filter
        if ($year === null) {
            $year = Carbon::now()->year;
        }
        if ($month === null) {
            $month = Carbon::now()->month;
        }

        $now        = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $startMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $startPrev  = $startMonth->copy()->subMonth()->startOfMonth();
        $endPrev    = $startMonth->copy()->subMonth()->endOfMonth();

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
            ->latest('created_at')
            ->limit(10)
            ->get(['id', 'nomor_permohonan', 'nama_pemohon', 'nik_pemohon', 'jenis_surat_id', 'kelurahan_id', 'status', 'created_at']);

        $data = [
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

        // ── Executive widgets (kabupaten / super_admin) ────────────────────
        if ($user->hasAnyRole(['admin_kabupaten', 'super_admin'])) {
            $data['is_executive']     = true;
            $data['current_month']    = $month;
            $data['current_year']     = $year;
            $data['daily_chart']      = $this->getDailySubmissionChart($baseQuery, $startMonth, $now);
            $data['kelurahan_map']    = $this->getKelurahanMapData($user, $startMonth, $now);
            $data['top_jenis_surat']  = $this->getTopJenisSurat($baseQuery, 8, $startMonth, $now);
            $data['sla_metrics']      = $this->getSlaMetrics($baseQuery, $startMonth, $now);
        } else {
            $data['is_executive'] = false;
        }

        return $data;
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
        // super_admin: no filter

        return $query;
    }

    /**
     * Hitung persentase perubahan antara dua nilai.
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

    /**
     * Grafik pengajuan harian per kelurahan dalam date range.
     * Return: ['labels' => [...], 'datasets' => [{ label, data, backgroundColor }, ...]]
     */
    private function getDailySubmissionChart($baseQuery, Carbon $start, Carbon $end): array
    {
        $start = $start->copy()->startOfDay();
        $end   = $end->copy()->endOfDay();

        // Ambil data per hari + kelurahan
        $rows = (clone $baseQuery)
            ->join('m_kelurahans', 'permohonan_surats.kelurahan_id', '=', 'm_kelurahans.id')
            ->whereBetween('permohonan_surats.created_at', [$start, $end])
            ->select(
                DB::raw('DATE(permohonan_surats.created_at) as tanggal'),
                'm_kelurahans.id',
                'm_kelurahans.nama as kelurahan_nama',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tanggal', 'm_kelurahans.id', 'm_kelurahans.nama')
            ->orderBy('tanggal')
            ->get();

        // Group by kelurahan, ambil unique kelurahans
        $kelurahanMap = [];
        $dailyTotals = [];

        foreach ($rows as $row) {
            $date = $row->tanggal;
            $kelId = $row->id;
            $kelNama = $row->kelurahan_nama;

            if (!isset($kelurahanMap[$kelId])) {
                $kelurahanMap[$kelId] = $kelNama;
            }

            if (!isset($dailyTotals[$date])) {
                $dailyTotals[$date] = [];
            }

            $dailyTotals[$date][$kelId] = (int) $row->total;
        }

        // Warna dataset (max 10 kelurahan)
        $colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#ec4899', '#06b6d4', '#f97316', '#6366f1', '#14b8a6',
        ];

        $datasets = [];
        $colorIdx = 0;

        foreach ($kelurahanMap as $kelId => $kelNama) {
            $data = [];
            $current = $start->copy();
            while ($current <= $end) {
                $dateKey = $current->format('Y-m-d');
                $data[] = (int) ($dailyTotals[$dateKey][$kelId] ?? 0);
                $current->addDay();
            }

            $datasets[] = [
                'label'           => $kelNama,
                'data'            => $data,
                'backgroundColor' => $colors[$colorIdx % count($colors)],
                'borderColor'     => $colors[$colorIdx % count($colors)],
                'borderWidth'     => 2,
                'fill'            => false,
                'tension'         => 0.35,
                'pointRadius'     => 2,
                'pointHoverRadius' => 5,
            ];

            $colorIdx++;
        }

        $labels = [];
        $current = $start->copy();
        while ($current <= $end) {
            $labels[] = $current->translatedFormat('d M');
            $current->addDay();
        }

        return ['labels' => $labels, 'datasets' => $datasets];
    }

    /**
     * Data peta per-kelurahan: jumlah pengajuan + koordinat + geojson.
     * Untuk kabupaten user → semua kelurahan dalam kabupaten.
     * Untuk super_admin → seluruh kelurahan yang punya pengajuan ATAU yang punya
     * koordinat (agar peta tetap menampilkan semua wilayah scope-nya).
     */
    private function getKelurahanMapData(User $user, Carbon $start, Carbon $end): array
    {
        $kelurahanQuery = Kelurahan::query()
            ->select([
                'm_kelurahans.id',
                'm_kelurahans.nama',
                'm_kelurahans.latitude',
                'm_kelurahans.longitude',
                'm_kelurahans.geojson_path',
                'm_kelurahans.kecamatan_id',
            ])
            ->with('kecamatan:id,nama');

        if ($user->hasRole('admin_kabupaten')) {
            $kelurahanQuery->whereHas('kecamatan', fn($q) => $q->where('kabupaten_id', $user->kabupaten_id));
        }

        // Hanya yang punya minimal koordinat (lat/lng) atau geojson; agar map tidak
        // menampilkan ribuan kelurahan se-Indonesia untuk super_admin.
        $kelurahanQuery->where(function ($q) {
            $q->whereNotNull('latitude')
                ->orWhereNotNull('geojson_path');
        });

        $kelurahans = $kelurahanQuery->get();

        if ($kelurahans->isEmpty()) {
            return ['features' => [], 'center' => null, 'max_count' => 0];
        }

        // Hitung jumlah pengajuan per kelurahan dalam periode — scope role
        $countsQuery = $this->buildBaseQuery($user)
            ->whereBetween('created_at', [$start, $end])
            ->select('kelurahan_id', DB::raw('COUNT(*) as total'))
            ->groupBy('kelurahan_id');

        $counts = $countsQuery->pluck('total', 'kelurahan_id');

        $features = [];
        $maxCount = 0;
        $latSum = 0.0;
        $lngSum = 0.0;
        $latLngCount = 0;

        foreach ($kelurahans as $kel) {
            $count = (int) ($counts[$kel->id] ?? 0);
            $maxCount = max($maxCount, $count);

            $geojsonUrl = null;
            if ($kel->geojson_path && Storage::disk('public')->exists($kel->geojson_path)) {
                $geojsonUrl = Storage::disk('public')->url($kel->geojson_path);
            }

            $features[] = [
                'id'          => $kel->id,
                'nama'        => $kel->nama,
                'kecamatan'   => $kel->kecamatan->nama ?? null,
                'lat'         => $kel->latitude !== null ? (float) $kel->latitude : null,
                'lng'         => $kel->longitude !== null ? (float) $kel->longitude : null,
                'geojson_url' => $geojsonUrl,
                'count'       => $count,
            ];

            if ($kel->latitude !== null && $kel->longitude !== null) {
                $latSum += (float) $kel->latitude;
                $lngSum += (float) $kel->longitude;
                $latLngCount++;
            }
        }

        $center = $latLngCount > 0
            ? ['lat' => $latSum / $latLngCount, 'lng' => $lngSum / $latLngCount]
            : null;

        return [
            'features'  => $features,
            'center'    => $center,
            'max_count' => $maxCount,
        ];
    }

    /**
     * Top jenis surat paling banyak diajukan dalam scope user + periode.
     */
    private function getTopJenisSurat($baseQuery, int $limit = 8, Carbon $start = null, Carbon $end = null): array
    {
        $query = (clone $baseQuery)
            ->join('jenis_surats', 'permohonan_surats.jenis_surat_id', '=', 'jenis_surats.id');

        if ($start && $end) {
            $query->whereBetween('permohonan_surats.created_at', [$start, $end]);
        }

        $rows = $query
            ->select('jenis_surats.id', 'jenis_surats.nama', 'jenis_surats.kode', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_surats.id', 'jenis_surats.nama', 'jenis_surats.kode')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        return [
            'labels' => $rows->pluck('kode')->toArray(),
            'names'  => $rows->pluck('nama')->toArray(),
            'data'   => $rows->pluck('total')->map(fn($v) => (int) $v)->toArray(),
        ];
    }

    /**
     * Metrik SLA proses surat dalam periode.
     * Hitung dari created_at → completed_at, hanya status completed.
     */
    private function getSlaMetrics($baseQuery, Carbon $start = null, Carbon $end = null): array
    {
        $query = (clone $baseQuery)
            ->whereNotNull('completed_at')
            ->where('status', 'completed');

        if ($start && $end) {
            $query->whereBetween('completed_at', [$start, $end]);
        }

        $stats = $query
            ->selectRaw('
                COUNT(*) as total,
                AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_minutes,
                MIN(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as min_minutes,
                MAX(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as max_minutes,
                SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, created_at, completed_at) <= ? THEN 1 ELSE 0 END) as within_target
            ', [self::SLA_TARGET_HOURS * 60])
            ->first();

        $total = (int) ($stats->total ?? 0);

        if ($total === 0) {
            return [
                'total'           => 0,
                'avg_human'       => '—',
                'min_human'       => '—',
                'max_human'       => '—',
                'within_pct'      => 0,
                'target_hours'    => self::SLA_TARGET_HOURS,
            ];
        }

        return [
            'total'        => $total,
            'avg_human'    => $this->formatMinutes((float) $stats->avg_minutes),
            'min_human'    => $this->formatMinutes((float) $stats->min_minutes),
            'max_human'    => $this->formatMinutes((float) $stats->max_minutes),
            'within_pct'   => $total > 0 ? round(((int) $stats->within_target / $total) * 100) : 0,
            'target_hours' => self::SLA_TARGET_HOURS,
        ];
    }

    /**
     * Format menit ke string human-readable Bahasa Indonesia.
     */
    private function formatMinutes(float $minutes): string
    {
        if ($minutes < 1) {
            return '<1 menit';
        }

        if ($minutes < 60) {
            return round($minutes) . ' menit';
        }

        $hours = $minutes / 60;
        if ($hours < 24) {
            return round($hours, 1) . ' jam';
        }

        $days = $hours / 24;
        return round($days, 1) . ' hari';
    }
}
