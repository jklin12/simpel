@extends('layouts.landing')

@section('title', 'Cek Status Permohonan')

@section('content')
<section class="bg-gray-50 min-h-screen pt-24 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Search Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Cek Status Permohonan</h1>
            <p class="text-gray-500 mb-8">Masukkan kode tracking yang Anda dapatkan saat pengajuan.</p>

            <form action="{{ route('tracking.search') }}" method="GET" class="max-w-md mx-auto">
                <div class="flex gap-2">
                    <input type="text" name="track_token" value="{{ request('track_token') }}" placeholder="Contoh: TRX12345678" class="flex-1 rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 uppercase tracking-widest text-center font-bold" required>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Cek
                    </button>
                </div>
                @error('track_token')
                <p class="mt-2 text-sm text-red-600 text-left">{{ $message }}</p>
                @enderror
            </form>
        </div>

        @if(isset($permohonan))
        <!-- Result -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Result -->
            <div class="bg-primary-50 p-6 border-b border-primary-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mb-2">
                        {{ $permohonan->jenisSurat->nama }}
                    </span>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-wide">{{ $permohonan->track_token }}</h2>
                    <p class="text-sm text-gray-500">Diajukan pada {{ $permohonan->created_at->isoFormat('D MMMM Y HH:mm') }}</p>
                </div>
                <div class="text-right">
                    @php
                    $statusColor = match($permohonan->status) {
                    'approved', 'completed' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'pending', 'in_review' => 'bg-yellow-100 text-yellow-800',
                    default => 'bg-gray-100 text-gray-800'
                    };
                    $statusLabel = match($permohonan->status) {
                    'approved' => 'Disetujui',
                    'completed' => 'Selesai',
                    'rejected' => 'Ditolak',
                    'pending' => 'Menunggu',
                    'in_review' => 'Sedang Diproses',
                    'draft' => 'Draft',
                    };
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <!-- Detail Info Permohonan -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Data Pemohon
                </h3>

                @php
                $d = $permohonan->data_permohonan ?? [];

                // Normalize field labels
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

                // Fields to skip (file references, boilerplate)
                $skipFields = ['_token', 'jenis_surat_id', 'kelurahan_id'];

                // Date fields for formatting
                $dateFields = ['tanggal_lahir', 'tanggal_surat_pengantar', 'tanggal_surat_pernyataan',
                'tanggal_meninggal', 'tanggal_surat'];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
                    {{-- Selalu tampilkan nama & NIK dari field utama --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Nama Pemohon</p>
                        <p class="font-medium text-gray-900 mt-0.5">{{ $permohonan->nama_pemohon }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">NIK Pemohon</p>
                        <p class="font-medium text-gray-900 mt-0.5">{{ $permohonan->nik_pemohon }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">No. WhatsApp</p>
                        <p class="font-medium text-gray-900 mt-0.5">{{ $permohonan->phone_pemohon ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">No. Permohonan</p>
                        <p class="font-medium text-gray-900 font-mono text-sm mt-0.5">{{ $permohonan->nomor_permohonan }}</p>
                    </div>
                    @if($permohonan->nomor_surat)
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Nomor Surat</p>
                        <p class="font-bold text-primary-700 mt-0.5">{{ $permohonan->nomor_surat }}</p>
                    </div>
                    @endif

                    {{-- Data JSON permohonan --}}
                    @foreach($d as $key => $value)
                    @if(in_array($key, $skipFields) || is_null($value) || $value === '') @continue @endif
                    @php
                    $label = $labelMap[$key] ?? ucwords(str_replace('_', ' ', $key));
                    $display = in_array($key, $dateFields)
                    ? \Carbon\Carbon::parse($value)->translatedFormat('d F Y')
                    : $value;
                    @endphp
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">{{ $label }}</p>
                        <p class="font-medium text-gray-900 mt-0.5">{{ $display }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Berkas Lampiran -->
            @if($permohonan->dokumens->isNotEmpty())
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    Berkas Lampiran
                    <span class="text-xs font-normal text-gray-400 ml-1">({{ $permohonan->dokumens->count() }} file)</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($permohonan->dokumens as $dok)
                    @php
                    $ext = strtolower(pathinfo($dok->original_name ?? $dok->file_path, PATHINFO_EXTENSION));
                    $isPdf = $ext === 'pdf';
                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                    $sizeKb = $dok->file_size ? round($dok->file_size / 1024) . ' KB' : '';
                    @endphp
                    <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-100 bg-gray-50 hover:bg-gray-100 transition-colors">
                        {{-- Icon --}}
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

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $dok->nama_dokumen ?? $dok->label }}</p>
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ $dok->original_name ?? basename($dok->file_path) }}</p>
                            @if($sizeKb)
                            <p class="text-xs text-gray-400">{{ $sizeKb }}</p>
                            @endif
                        </div>

                        {{-- Badge status --}}
                        <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 mt-0.5">
                            ✓ Terupload
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Timeline History -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Riwayat Status</h3>

                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($permohonan->approvals as $approval)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white 
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
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $approval->step_name ?? 'Verifikasi' }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            @if($approval->updated_at != $approval->created_at)
                                            <time datetime="{{ $approval->updated_at }}">{{ $approval->updated_at->isoFormat('D MMM HH:mm') }}</time>
                                            @else
                                            <span class="text-gray-400">Menunggu</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($approval->catatan)
                                <div class="ml-11 mt-2 p-3 bg-gray-50 rounded-lg text-sm text-gray-600 border border-gray-100">
                                    <strong>Catatan:</strong> {{ $approval->catatan }}
                                </div>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Download Surat (hanya tampil jika status completed & file tersedia) -->
            @if($permohonan->status === 'completed' && $permohonan->signed_file_path)
            <div class="p-6 bg-green-50 border-t border-green-100">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-green-900 text-sm">Surat Sudah Selesai & Siap Diunduh</p>
                            <p class="text-xs text-green-700 mt-0.5">File PDF surat yang sudah ditandatangani tersedia untuk diunduh.</p>
                        </div>
                    </div>
                    <a href="{{ route('tracking.download.signed', $permohonan->track_token) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Unduh Surat
                    </a>
                </div>
            </div>
            @endif

        </div>
        @endif
    </div>
</section>
@endsection