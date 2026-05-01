@extends('layouts.app')

@section('title', 'Dashboard Eksekutif')

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Eksekutif 📊</h1>
            <p class="text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}. Pantau performa pelayanan surat lintas wilayah.</p>
        </div>
        <!-- Filter Bulan/Tahun -->
        <form method="GET" class="flex items-center gap-2 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
            <select name="month" class="px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @php $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']; @endphp
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m == $current_month ? 'selected' : '' }}>
                        {{ $months[$m - 1] }}
                    </option>
                @endfor
            </select>
            <select name="year" class="px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @for ($y = date('Y') - 3; $y <= date('Y'); $y++)
                    <option value="{{ $y }}" {{ $y == $current_year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Tampilkan
            </button>
        </form>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @if(Auth::user()->kabupaten)
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.496-1.868l-7-4z" clip-rule="evenodd" />
            </svg>
            Kabupaten {{ Auth::user()->kabupaten->nama }}
        </span>
        @endif
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
            {{ ucwords(str_replace('_', ' ', Auth::user()->getRoleNames()->first())) }}
        </span>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-sm font-medium text-gray-500 mb-1">Permohonan Masuk</h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['permohonan_masuk']) }}</p>
            <div class="mt-2 flex items-center text-xs font-medium
                    {{ $stats['permohonan_masuk_pct']['direction'] === 'up' ? 'text-green-600' : ($stats['permohonan_masuk_pct']['direction'] === 'down' ? 'text-red-500' : 'text-gray-400') }}">
                <span class="px-1.5 py-0.5 rounded mr-1
                        {{ $stats['permohonan_masuk_pct']['direction'] === 'up' ? 'bg-green-50' : ($stats['permohonan_masuk_pct']['direction'] === 'down' ? 'bg-red-50' : 'bg-gray-100') }}">
                    {{ $stats['permohonan_masuk_pct']['direction'] === 'up' ? '+' : ($stats['permohonan_masuk_pct']['direction'] === 'down' ? '-' : '') }}{{ $stats['permohonan_masuk_pct']['value'] }}%
                </span>
                <span>dari bulan lalu</span>
            </div>
        </div>
        <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-blue-600 -mr-6 -mt-6" fill="currentcolor" viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14h-4.83l-3.17 3.17L8.83 16H4V4h16v12z" />
            </svg>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-sm font-medium text-gray-500 mb-1">Menunggu Verifikasi</h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['menunggu_verifikasi']) }}</p>
            <div class="mt-2 flex items-center text-xs font-medium
                    {{ $stats['menunggu_verifikasi'] > 0 ? 'text-orange-600' : 'text-green-600' }}">
                <span class="{{ $stats['menunggu_verifikasi'] > 0 ? 'bg-orange-50' : 'bg-green-50' }} px-1.5 py-0.5 rounded mr-1">
                    {{ $stats['menunggu_verifikasi'] > 0 ? 'Antrian' : 'Bersih' }}
                </span>
                <span>{{ $stats['menunggu_verifikasi'] > 0 ? 'perlu tindakan' : 'semua terproses' }}</span>
            </div>
        </div>
        <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-orange-500 -mr-6 -mt-6" fill="currentcolor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
            </svg>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-sm font-medium text-gray-500 mb-1">Selesai Diproses</h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['selesai_diproses']) }}</p>
            <div class="mt-2 flex items-center text-xs font-medium
                    {{ $stats['selesai_pct']['direction'] === 'up' ? 'text-blue-600' : ($stats['selesai_pct']['direction'] === 'down' ? 'text-red-500' : 'text-gray-400') }}">
                <span class="px-1.5 py-0.5 rounded mr-1
                        {{ $stats['selesai_pct']['direction'] === 'up' ? 'bg-blue-50' : ($stats['selesai_pct']['direction'] === 'down' ? 'bg-red-50' : 'bg-gray-100') }}">
                    {{ $stats['selesai_pct']['direction'] === 'up' ? '+' : ($stats['selesai_pct']['direction'] === 'down' ? '-' : '') }}{{ $stats['selesai_pct']['value'] }}%
                </span>
                <span>dari bulan lalu</span>
            </div>
        </div>
        <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-green-500 -mr-6 -mt-6" fill="currentcolor" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
            </svg>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Jenis Surat</h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_jenis_surat']) }}</p>
            <div class="mt-2 flex items-center text-xs text-gray-500 font-medium">
                <span>Layanan tersedia</span>
            </div>
        </div>
        <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-purple-600 -mr-6 -mt-6" fill="currentcolor" viewBox="0 0 24 24">
                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
            </svg>
        </div>
    </div>
</div>

<!-- Daily Chart -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="font-bold text-gray-800">Pengajuan Surat Harian</h3>
            @php $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $months[$current_month - 1] }} {{ $current_year }}
            </p>
        </div>
    </div>
    <div class="h-72">
        <canvas id="dailyChart"></canvas>
    </div>
</div>

<!-- Map + Top Jenis Surat -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Map -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="font-bold text-gray-800">Sebaran Pengajuan per Kelurahan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Klik marker untuk detail. Polygon = batas wilayah kelurahan (jika tersedia).</p>
            </div>
            <div class="flex items-center gap-3 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-blue-500/30 border border-blue-500"></span>
                    Sedikit
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-blue-600/70 border border-blue-700"></span>
                    Banyak
                </span>
            </div>
        </div>

        @if(empty($kelurahan_map['features']))
        <div class="h-96 flex flex-col items-center justify-center text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="text-gray-500 font-medium">Belum ada data koordinat kelurahan</p>
            <p class="text-xs text-gray-400 mt-1">Tambahkan latitude/longitude atau geojson di master data kelurahan.</p>
        </div>
        @else
        <div id="kelurahanMap" class="h-96 rounded-xl border border-gray-100 overflow-hidden z-0"></div>
        @endif
    </div>

    <!-- Top Jenis Surat -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="mb-4">
            <h3 class="font-bold text-gray-800">Jenis Surat Terbanyak</h3>
            <p class="text-xs text-gray-500 mt-0.5">Top {{ count($top_jenis_surat['data']) ?: 8 }}</p>
        </div>
        @if(empty($top_jenis_surat['data']))
        <div class="h-72 flex items-center justify-center text-sm text-gray-400">
            Belum ada data pengajuan.
        </div>
        @else
        <div class="h-72">
            <canvas id="topJenisChart"></canvas>
        </div>
        @endif
    </div>
</div>

<!-- SLA Proses Surat per Kelurahan -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-50">
        <h3 class="font-bold text-gray-800">SLA Proses Surat per Kelurahan</h3>
        @php $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
        <p class="text-xs text-gray-500 mt-1">{{ $months[$current_month - 1] }} {{ $current_year }}</p>
    </div>
    <div class="overflow-x-auto">
        @if(!empty($sla_per_kelurahan) && count($sla_per_kelurahan) > 0)
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelurahan</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Total Selesai</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Rata-rata</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Tercepat</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Terlama</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($sla_per_kelurahan as $sla)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $sla['kelurahan_nama'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                            {{ $sla['total'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 text-right font-medium">{{ $sla['avg_human'] }}</td>
                    <td class="px-6 py-4 text-sm text-green-600 text-right font-medium">{{ $sla['min_human'] }}</td>
                    <td class="px-6 py-4 text-sm text-red-600 text-right font-medium">{{ $sla['max_human'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-gray-500">
            <p>Belum ada surat yang selesai diproses pada periode ini.</p>
        </div>
        @endif
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Permohonan Terbaru</h3>
        <a href="{{ route('admin.permohonan-surat.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        @if($permohonan_terbaru->isNotEmpty())
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemohon</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Surat</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelurahan</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($permohonan_terbaru as $item)
                @php
                    $initials = collect(explode(' ', $item->nama_pemohon))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                    $statusConfig = match($item->status) {
                        'pending'   => ['bg' => 'bg-orange-100 text-orange-700 border-orange-100', 'label' => 'Pending'],
                        'approved'  => ['bg' => 'bg-blue-100 text-blue-700 border-blue-100', 'label' => 'Disetujui'],
                        'completed' => ['bg' => 'bg-green-100 text-green-700 border-green-100', 'label' => 'Selesai'],
                        'rejected'  => ['bg' => 'bg-red-100 text-red-700 border-red-100', 'label' => 'Ditolak'],
                        default     => ['bg' => 'bg-gray-100 text-gray-600 border-gray-100', 'label' => ucfirst($item->status)],
                    };
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs flex-shrink-0">
                                {{ $initials }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ $item->nama_pemohon }}</p>
                                <p class="text-xs text-gray-400">{{ $item->nik_pemohon }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $item->jenisSurat->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $item->kelurahan->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $item->created_at->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusConfig['bg'] }}">
                            {{ $statusConfig['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.permohonan-surat.show', $item->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-gray-500">
            <p>Belum ada permohonan surat.</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
(function() {
    const dailyData = @json($daily_chart);
    const topJenis  = @json($top_jenis_surat);
    const mapData   = @json($kelurahan_map);

    // ── Daily submission line chart (per kelurahan) ─────────────────────
    const dailyCanvas = document.getElementById('dailyChart');
    if (dailyCanvas && dailyData.labels && dailyData.datasets) {
        new Chart(dailyCanvas, {
            type: 'line',
            data: {
                labels: dailyData.labels,
                datasets: dailyData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#6b7280',
                            font: { size: 11, weight: '500' },
                            boxWidth: 8,
                            padding: 12,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: (ctx) => ctx.dataset.label + ': ' + ctx.parsed.y + ' permohonan'
                        }
                    }
                },
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        ticks: { precision: 0, color: '#9ca3af' },
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        ticks: { color: '#9ca3af', maxRotation: 0, autoSkip: true, maxTicksLimit: 10 },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ── Top jenis surat horizontal bar ──────────────────────────────────
    const topCanvas = document.getElementById('topJenisChart');
    if (topCanvas && topJenis.data && topJenis.data.length) {
        new Chart(topCanvas, {
            type: 'bar',
            data: {
                labels: topJenis.labels,
                datasets: [{
                    label: 'Pengajuan',
                    data: topJenis.data,
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                    barThickness: 18
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 10,
                        callbacks: {
                            title: (items) => topJenis.names[items[0].dataIndex] || items[0].label,
                            label: (ctx) => ctx.parsed.x + ' pengajuan'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#9ca3af' },
                        grid: { color: '#f3f4f6' }
                    },
                    y: {
                        ticks: { color: '#374151', font: { weight: '500' } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ── Kelurahan map (Leaflet + OSM) ───────────────────────────────────
    const mapContainer = document.getElementById('kelurahanMap');
    if (mapContainer && mapData.features && mapData.features.length) {
        const center = mapData.center || { lat: -3.4421, lng: 114.7567 };
        const map = L.map('kelurahanMap', {
            scrollWheelZoom: false
        }).setView([center.lat, center.lng], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const maxCount = Math.max(mapData.max_count, 1);

        // Palet warna tegas untuk setiap kelurahan (distinct colors)
        const kelurahanColors = [
            '#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e',
            '#10b981', '#06b6d4', '#0ea5e9', '#3b82f6', '#6366f1',
            '#8b5cf6', '#d946ef', '#ec4899', '#f43f5e', '#a16207',
            '#7c3aed', '#059669', '#0891b2', '#1e40af', '#dc2626'
        ];

        // Generate warna based on index (cycling through palette jika lebih dari 20 kelurahan)
        const colorByIndex = (index) => {
            return kelurahanColors[index % kelurahanColors.length];
        };

        // Intensitas warna berdasarkan count (untuk menunjukkan volume)
        const intensifyColor = (hexColor, count) => {
            if (count === 0) {
                return '#e5e7eb'; // grey untuk 0
            }
            const t = count / maxCount; // 0 to 1
            // Jika count rendah, use lighter shade; tinggi = saturated
            if (t < 0.3) {
                // Lighten: convert hex to lighter version
                const r = parseInt(hexColor.slice(1,3), 16);
                const g = parseInt(hexColor.slice(3,5), 16);
                const b = parseInt(hexColor.slice(5,7), 16);
                const lighter = Math.round(r * 0.7 + 255 * 0.3);
                const lightg = Math.round(g * 0.7 + 255 * 0.3);
                const lightb = Math.round(b * 0.7 + 255 * 0.3);
                return '#' + [lighter, lightg, lightb].map(x => x.toString(16).padStart(2, '0')).join('');
            }
            return hexColor;
        };

        // Hitung centroid dari polygon geometry
        const getCentroid = (geometry) => {
            let latSum = 0, lngSum = 0, count = 0;
            const processCoords = (coords, depth) => {
                if (depth === 2) {
                    coords.forEach(c => {
                        lngSum += c[0]; latSum += c[1]; count++;
                    });
                } else {
                    coords.forEach(c => processCoords(c, depth + 1));
                }
            };
            if (geometry.type === 'MultiPolygon') {
                processCoords(geometry.coordinates, 0);
            } else if (geometry.type === 'Polygon') {
                processCoords(geometry.coordinates, 0);
            }
            return count > 0 ? [latSum / count, lngSum / count] : null;
        };

        // Custom icon untuk menampilkan count badge
        const countBadgeIcon = (count, color) => {
            return L.divIcon({
                html: `<div style="
                    background: ${color};
                    color: white;
                    border-radius: 50%;
                    width: 32px;
                    height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    font-size: 13px;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
                    border: 2px solid white;
                ">${count}</div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                className: 'leaflet-count-badge'
            });
        };

        const bounds = [];

        mapData.features.forEach((f, index) => {
            // Assign warna unik untuk setiap kelurahan (solid base color)
            const baseColor = colorByIndex(index);
            // Intensity hanya untuk count badge
            const badgeColor = intensifyColor(baseColor, f.count);

            const popupHtml = `
                <div class="text-sm">
                    <div class="font-semibold text-gray-800">${f.nama}</div>
                    ${f.kecamatan ? `<div class="text-xs text-gray-500">Kec. ${f.kecamatan}</div>` : ''}
                    <div class="mt-1.5 text-xs">Pengajuan: <strong>${f.count}</strong></div>
                </div>`;

            // Polygon dari geojson data (sudah di-load di backend)
            if (f.geojson) {
                const layer = L.geoJSON(f.geojson, {
                    style: {
                        color: baseColor,
                        weight: 2.5,
                        fillColor: baseColor,
                        fillOpacity: 0.4,
                        dashArray: f.count === 0 ? '5,5' : ''
                    },
                    onEachFeature: (feature, layer) => {
                        layer.bindPopup(popupHtml);
                        layer.on('mouseover', function() { this.setStyle({ weight: 3.5, fillOpacity: 0.6 }); });
                        layer.on('mouseout', function() { this.setStyle({ weight: 2.5, fillOpacity: 0.4 }); });
                    }
                }).addTo(map);

                // Hitung centroid dan tampilkan count badge dengan intensity color
                if (f.geojson.features && f.geojson.features[0] && f.geojson.features[0].geometry) {
                    const centroid = getCentroid(f.geojson.features[0].geometry);
                    if (centroid) {
                        L.marker(centroid, {
                            icon: countBadgeIcon(f.count, badgeColor),
                            interactive: false,
                            zIndexOffset: 100
                        }).addTo(map);
                    }
                }
                try { map.fitBounds(layer.getBounds().pad(0.1)); } catch(e) {}
            }

            // Circle marker sebagai indikator titik + fallback
            if (f.lat !== null && f.lng !== null) {
                L.circleMarker([f.lat, f.lng], {
                    radius: 7,
                    color: baseColor,
                    fillColor: baseColor,
                    fillOpacity: 0.8,
                    weight: 2.5,
                    stroke: true,
                    opacity: 1
                }).bindPopup(popupHtml).addTo(map);
                bounds.push([f.lat, f.lng]);
            }
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [30, 30] });
        } else if (bounds.length === 1) {
            map.setView(bounds[0], 13);
        }
    }
})();
</script>
@endpush
@endsection
