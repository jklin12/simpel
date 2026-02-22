@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }} 👋</h1>
        <p class="text-gray-500 mt-1">Berikut adalah ringkasan aktivitas surat bulan ini.</p>
        
        <!-- Location Badge -->
        <div class="mt-4 flex flex-wrap gap-2">
            @if(Auth::user()->kelurahan)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    Kelurahan {{ Auth::user()->kelurahan->nama }}
                </span>
            @endif
            @if(Auth::user()->kecamatan)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.496-1.868l-7-4zM6 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm3 1a1 1 0 012 0v3a1 1 0 11-2 0v-3zm5-1a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Kecamatan {{ Auth::user()->kecamatan->nama }}
                </span>
            @endif
             <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                {{ ucwords(str_replace('_', ' ', Auth::user()->getRoleNames()->first())) }}
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Permohonan Masuk -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Permohonan Masuk</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['permohonan_masuk'] }}</p>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-blue-600 -mr-6 -mt-6" fill="currentcolor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14h-4.83l-3.17 3.17L8.83 16H4V4h16v12z"/></svg>
            </div>
        </div>

        <!-- Card 2: Menunggu Verifikasi -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Menunggu Verifikasi</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['menunggu_verifikasi'] }}</p>
                <div class="mt-2 flex items-center text-xs font-medium
                    {{ $stats['menunggu_verifikasi'] > 0 ? 'text-orange-600' : 'text-green-600' }}">
                    <span class="{{ $stats['menunggu_verifikasi'] > 0 ? 'bg-orange-50' : 'bg-green-50' }} px-1.5 py-0.5 rounded mr-1">
                        {{ $stats['menunggu_verifikasi'] > 0 ? 'Urgent' : 'Bersih' }}
                    </span>
                    <span>{{ $stats['menunggu_verifikasi'] > 0 ? 'perlu tindakan' : 'semua terproses' }}</span>
                </div>
            </div>
            <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-orange-500 -mr-6 -mt-6" fill="currentcolor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
        </div>

        <!-- Card 3: Selesai Diproses -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Selesai Diproses</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['selesai_diproses'] }}</p>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-green-500 -mr-6 -mt-6" fill="currentcolor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
        </div>

        <!-- Card 4: Total Jenis Surat -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Total Jenis Surat</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_jenis_surat'] }}</p>
                <div class="mt-2 flex items-center text-xs text-gray-500 font-medium">
                    <span>Layanan tersedia</span>
                </div>
            </div>
            <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-purple-600 -mr-6 -mt-6" fill="currentcolor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            </div>
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
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($permohonan_terbaru as $item)
                    @php
                        $initials = collect(explode(' ', $item->nama_pemohon))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                        $statusConfig = match($item->status) {
                            'pending'   => ['bg' => 'bg-orange-100 text-orange-700 border-orange-100', 'label' => 'Pending'],
                            'approved'  => ['bg' => 'bg-blue-100 text-blue-700 border-blue-100',   'label' => 'Disetujui'],
                            'completed' => ['bg' => 'bg-green-100 text-green-700 border-green-100', 'label' => 'Selesai'],
                            'rejected'  => ['bg' => 'bg-red-100 text-red-700 border-red-100',       'label' => 'Ditolak'],
                            default     => ['bg' => 'bg-gray-100 text-gray-600 border-gray-100',    'label' => ucfirst($item->status)],
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
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->jenisSurat->nama ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $item->created_at->translatedFormat('d M Y') }}
                        </td>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>Belum ada permohonan surat.</p>
            </div>
            @endif
        </div>
    </div>
@endsection
