@extends('layouts.landing')

@section('title', 'Cek Status Permohonan')

@section('content')
@php
    $hasResult = isset($permohonan);
    $canDownload = $hasResult && $permohonan->status === 'completed' && $permohonan->signed_file_path;
    $isRejected = $hasResult && $permohonan->status === 'rejected';
    $hasStickyAction = $canDownload || $isRejected;
@endphp
<section class="bg-gray-50 min-h-screen pt-4 {{ $hasStickyAction ? 'pb-28' : 'pb-12' }}">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

        {{-- Pencarian --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h1 class="text-xl font-bold text-gray-900">Cek Status Permohonan</h1>
            <p class="text-[13px] text-gray-500 mt-1">Masukkan kode tracking yang Anda dapatkan saat pengajuan (juga dikirim lewat WhatsApp).</p>

            <form action="{{ route('layanan.surat.tracking.search') }}" method="GET" class="mt-4">
                <label for="track_token" class="sr-only">Kode tracking</label>
                <input id="track_token" type="text" name="track_token" value="{{ request('track_token') }}"
                    placeholder="Contoh: TRX12345678" maxlength="20" autocomplete="off" autocapitalize="characters"
                    class="w-full h-14 rounded-xl border-gray-300 focus:ring-primary-500 focus:border-primary-500 uppercase tracking-widest text-center text-lg font-bold" required>
                @error('track_token')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <button type="submit" class="mt-3 w-full h-12 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors">
                    Cek Status
                </button>
            </form>

            <p class="mt-3 text-[13px] text-gray-500 text-center">
                Belum punya kode?
                <a href="{{ route('layanan.index') }}" class="font-semibold text-primary-600 underline">Ajukan permohonan</a>
            </p>
        </div>

        @if($hasResult)
        @push('scripts')
        <script>
            if (typeof gtag !== 'undefined') {
                gtag('event', 'tracking_status_viewed', {
                    jenis_surat: '{{ $permohonan->jenisSurat->kode ?? '' }}',
                    status: '{{ $permohonan->status }}'
                });
            }
        </script>
        @endpush

        @php
            $statusColor = match($permohonan->status) {
                'approved', 'completed' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
                'pending', 'in_review' => 'bg-yellow-100 text-yellow-800',
                default => 'bg-gray-100 text-gray-800',
            };
            $statusLabel = match($permohonan->status) {
                'approved' => 'Disetujui',
                'completed' => 'Selesai',
                'rejected' => 'Ditolak',
                'pending' => 'Menunggu',
                'in_review' => 'Sedang Diproses',
                'draft' => 'Draft',
                default => ucfirst($permohonan->status),
            };
        @endphp

        {{-- Ringkasan: kode, jenis surat, tanggal, status --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-start justify-between gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                    {{ $permohonan->jenisSurat->nama }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold flex-none {{ $statusColor }}">{{ $statusLabel }}</span>
            </div>

            <div class="mt-3 flex items-center gap-2" x-data="{ copied: false }">
                <h2 class="text-2xl font-bold text-gray-900 tracking-wide break-all">{{ $permohonan->track_token }}</h2>
                <button type="button" aria-label="Salin kode tracking"
                    @click="navigator.clipboard && navigator.clipboard.writeText('{{ $permohonan->track_token }}').then(() => { copied = true; setTimeout(() => copied = false, 1800) })"
                    class="w-11 h-11 -my-2 flex-none flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg x-show="!copied" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" /><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" /></svg>
                    <span x-show="copied" x-cloak class="text-xs font-semibold text-green-600">Tersalin</span>
                </button>
            </div>
            <p class="text-[13px] text-gray-500">Diajukan pada {{ $permohonan->created_at->isoFormat('D MMMM Y HH:mm') }}</p>

            @if(($permohonan->revision_count ?? 0) > 0)
            <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Revisi ke-{{ $permohonan->revision_count }}
            </span>
            @endif
        </div>

        {{-- Surat selesai: kartu informasi (tombol Unduh ada di bar bawah) --}}
        @if($canDownload)
        <div class="rounded-2xl bg-green-50 border border-green-100 p-4 flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-green-900 text-sm">Surat sudah selesai &amp; siap diunduh</p>
                <p class="text-xs text-green-700 mt-0.5">File PDF surat yang sudah ditandatangani tersedia untuk diunduh.</p>
                @if($permohonan->nomor_surat)
                <p class="text-xs text-green-800 mt-2">Nomor surat: <span class="font-bold">{{ $permohonan->nomor_surat }}</span></p>
                @endif
            </div>
        </div>
        @endif

        {{-- Ditolak: alasan (tombol Revisi ada di bar bawah) --}}
        @if($isRejected)
        <div class="rounded-2xl bg-red-50 border border-red-100 p-4 flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-red-900 text-sm">Permohonan ditolak</p>
                @if($permohonan->rejected_reason)
                <p class="text-[13px] text-red-700 mt-0.5 leading-relaxed">{{ $permohonan->rejected_reason }}</p>
                @endif
                <p class="text-xs text-red-600 mt-1">Anda dapat memperbaiki dan mengajukan ulang permohonan ini.</p>
            </div>
        </div>
        @endif

        {{-- Riwayat status (timeline) --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-900 mb-5">Riwayat Status</h3>
            <div class="flow-root">
                <ul role="list" class="-mb-6">
                    @foreach($permohonan->approvals as $approval)
                    <li>
                        <div class="relative pb-6">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-4 ring-white
                                        {{ $approval->status == 'approved' ? 'bg-green-500' : ($approval->status == 'rejected' ? 'bg-red-500' : 'bg-gray-400') }}">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($approval->status == 'approved')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            @elseif($approval->status == 'rejected')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            @endif
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between gap-3">
                                    <p class="text-sm text-gray-700">{{ $approval->step_name ?? 'Verifikasi' }}</p>
                                    <div class="text-right text-[13px] text-gray-500 flex-none">
                                        @if($approval->updated_at != $approval->created_at)
                                        <time datetime="{{ $approval->updated_at }}">{{ $approval->updated_at->isoFormat('D MMM HH:mm') }}</time>
                                        @else
                                        <span class="text-gray-400">Menunggu</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($approval->catatan)
                            <div class="ml-11 mt-2 p-3 bg-gray-50 rounded-lg text-[13px] text-gray-600 border border-gray-100">
                                <strong>Catatan:</strong> {{ $approval->catatan }}
                            </div>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        @php
            $d = $permohonan->data_permohonan ?? [];

            $labelMap = [
                'nama_lengkap' => 'Nama Lengkap',
                'nik_bersangkutan' => 'NIK',
                'jenis_kelamin' => 'Jenis Kelamin',
                'agama' => 'Agama',
                'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir',
                'status_perkawinan' => 'Status Perkawinan',
                'pekerjaan' => 'Pekerjaan',
                'pendidikan_terakhir' => 'Pendidikan Terakhir',
                'alamat_lengkap' => 'Alamat Sesuai KTP',
                'keperluan' => 'Keperluan',
                'keperluan_sktm' => 'Keperluan',
                'rt' => 'RT',
                'rw' => 'RW',
                'no_surat_pengantar' => 'No. Surat Pengantar RT/RW',
                'tanggal_surat_pengantar' => 'Tanggal Surat Pengantar',
                'no_surat_pernyataan' => 'No. Surat Pernyataan',
                'tanggal_surat_pernyataan' => 'Tanggal Surat Pernyataan',
                // SKM fields
                'nama_alm' => 'Nama Almarhum/ah',
                'nik_alm' => 'NIK Almarhum/ah',
                'tanggal_meninggal' => 'Tanggal Meninggal',
                'tempat_meninggal' => 'Tempat Meninggal',
                'sebab_meninggal' => 'Sebab Meninggal',
            ];

            $skipFields = ['_token', 'jenis_surat_id', 'kelurahan_id'];

            $dateFields = ['tanggal_lahir', 'tanggal_surat_pengantar', 'tanggal_surat_pernyataan',
                'tanggal_meninggal', 'tanggal_surat'];

            // NIK dimasking pada halaman tracking publik — cukup tampilkan
            // 4 digit awal + 4 digit akhir (mis. 3374••••••••0001).
            $maskNik = function ($nik) {
                $nik = preg_replace('/\s+/', '', (string) $nik);
                $len = strlen($nik);
                if ($len <= 4) {
                    return $nik;
                }
                if ($len >= 8) {
                    return substr($nik, 0, 4) . str_repeat('•', $len - 8) . substr($nik, -4);
                }
                return str_repeat('•', $len - 4) . substr($nik, -4);
            };
        @endphp

        {{-- Data pemohon (accordion, tertutup secara bawaan) --}}
        <div x-data="{ open: false }" class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <button type="button" @click="open = !open" :aria-expanded="open" class="w-full flex items-center justify-between gap-3 px-5 h-14 text-left">
                <span class="flex items-center gap-2 text-base font-bold text-gray-900">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Data Pemohon
                </span>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open" x-cloak class="px-5 pb-5 border-t border-gray-100 pt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase font-semibold tracking-wide">Nama Pemohon</p>
                        <p class="font-medium text-gray-900 mt-0.5">{{ $permohonan->nama_pemohon }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase font-semibold tracking-wide">NIK Pemohon</p>
                        <p class="font-medium text-gray-900 mt-0.5 font-mono tracking-wider">{{ $maskNik($permohonan->nik_pemohon) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase font-semibold tracking-wide">No. WhatsApp</p>
                        <p class="font-medium text-gray-900 mt-0.5">{{ $permohonan->phone_pemohon ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase font-semibold tracking-wide">No. Permohonan</p>
                        <p class="font-medium text-gray-900 font-mono text-sm mt-0.5">{{ $permohonan->nomor_permohonan }}</p>
                    </div>
                    @if($permohonan->nomor_surat)
                    <div class="sm:col-span-2">
                        <p class="text-[11px] text-gray-400 uppercase font-semibold tracking-wide">Nomor Surat</p>
                        <p class="font-bold text-primary-700 mt-0.5">{{ $permohonan->nomor_surat }}</p>
                    </div>
                    @endif

                    @foreach($d as $key => $value)
                    @if(in_array($key, $skipFields) || is_null($value) || $value === '') @continue @endif
                    @php
                        $label = $labelMap[$key] ?? ucwords(str_replace('_', ' ', $key));
                        $display = in_array($key, $dateFields)
                            ? \Carbon\Carbon::parse($value)->translatedFormat('d F Y')
                            : (str_contains($key, 'nik') ? $maskNik($value) : $value);
                    @endphp
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase font-semibold tracking-wide">{{ $label }}</p>
                        <p class="font-medium text-gray-900 mt-0.5 break-words">{{ $display }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Berkas lampiran (accordion, tertutup secara bawaan) --}}
        @if($permohonan->dokumens->isNotEmpty())
        <div x-data="{ open: false }" class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <button type="button" @click="open = !open" :aria-expanded="open" class="w-full flex items-center justify-between gap-3 px-5 h-14 text-left">
                <span class="flex items-center gap-2 text-base font-bold text-gray-900">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    Berkas Lampiran
                    <span class="text-xs font-normal text-gray-400">({{ $permohonan->dokumens->count() }} file)</span>
                </span>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open" x-cloak class="px-5 pb-5 border-t border-gray-100 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($permohonan->dokumens as $dok)
                @php
                    $ext = strtolower(pathinfo($dok->original_name ?? $dok->file_path, PATHINFO_EXTENSION));
                    $isPdf = $ext === 'pdf';
                    $sizeKb = $dok->file_size ? round($dok->file_size / 1024) . ' KB' : '';
                @endphp
                <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-100 bg-gray-50">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $isPdf ? 'bg-red-100' : 'bg-blue-100' }}">
                        @if($isPdf)
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                        @else
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $dok->nama_dokumen ?? $dok->label }}</p>
                        <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $dok->original_name ?? basename($dok->file_path) }}</p>
                        @if($sizeKb)
                        <p class="text-[11px] text-gray-400">{{ $sizeKb }}</p>
                        @endif
                    </div>
                    <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-100 text-green-700 mt-0.5">✓ Terupload</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif
    </div>
</section>

{{-- Aksi utama menempel di bawah layar --}}
@if($canDownload)
<div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-gray-200 px-4 py-3">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('layanan.surat.tracking.download', $permohonan->track_token) }}"
            onclick="if (typeof gtag !== 'undefined') { gtag('event', 'surat_downloaded', { jenis_surat: '{{ $permohonan->jenisSurat->kode ?? '' }}' }); }"
            class="flex items-center justify-center gap-2 h-12 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Unduh Surat
        </a>
    </div>
</div>
@endif

@if($isRejected)
<div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-gray-200 px-4 py-3">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('layanan.surat.revisi', $permohonan->track_token) }}"
            class="flex items-center justify-center gap-2 h-12 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Revisi &amp; Ajukan Ulang
        </a>
    </div>
</div>
@endif
@endsection
