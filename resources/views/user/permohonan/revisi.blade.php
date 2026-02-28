@extends('layouts.landing')

@section('title', 'Revisi Permohonan — ' . $permohonan->jenisSurat->nama)

@php
/**
* Pre-fill strategy: flash the existing data_permohonan into the session as "old" input
* so all @include('user.permohonan.types.*') partials render with existing values via old().
* We only populate when the user has NOT just submitted the form (i.e., no existing old input).
*/
$existingData = $permohonan->data_permohonan ?? [];
if (!session()->hasOldInput()) {
session()->flashInput(array_merge($existingData, [
'no_wa' => $permohonan->phone_pemohon,
'nama_lengkap' => $permohonan->nama_pemohon,
'nik_bersangkutan' => $permohonan->nik_pemohon,
'nama_pelapor' => $permohonan->nama_pemohon,
'nik_pelapor' => $permohonan->nik_pemohon,
'alamat_pelapor' => $permohonan->alamat_pemohon,
'alamat_lengkap' => $existingData['alamat_lengkap'] ?? $permohonan->alamat_pemohon,
]));
}
$service = $permohonan->jenisSurat;
$kelurahan = $permohonan->kelurahan;
@endphp

@section('content')
<div class="bg-gray-50 min-h-screen pt-24 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-6">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600 font-bold text-xl">
                    {{ substr($service->kode, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Revisi Permohonan — {{ $service->nama }}</h1>
                    <p class="text-gray-500">{{ $kelurahan->nama }}, Kecamatan {{ $kelurahan->kecamatan->nama ?? 'Landasan Ulin' }}</p>
                </div>
            </div>

            {{-- Meta permohonan --}}
            <div class="flex flex-wrap gap-3 text-sm">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-600 font-mono">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                    {{ $permohonan->track_token }}
                </span>
                @if($permohonan->revision_count > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-700 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Revisi ke-{{ $permohonan->revision_count + 1 }}
                </span>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Ditolak
                </span>
            </div>
        </div>

        {{-- Panel Alasan Penolakan --}}
        @if($permohonan->rejected_reason)
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6 flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-red-800 text-sm mb-1">Alasan Penolakan dari Admin</p>
                <p class="text-red-700 text-sm leading-relaxed">{{ $permohonan->rejected_reason }}</p>
            </div>
        </div>
        @endif

        {{-- Info Revisi --}}
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6 flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-sm text-blue-700">
                <p class="font-semibold mb-1">Petunjuk Revisi</p>
                <ul class="list-disc list-inside space-y-1 text-blue-600">
                    <li>Perbaiki data sesuai alasan penolakan di atas.</li>
                    <li>Data lama sudah ter-isi otomatis — ubah hanya yang perlu diperbaiki.</li>
                    <li>Untuk berkas: <strong>kosongkan input file</strong> jika tidak ingin mengganti dokumen lama.</li>
                </ul>
            </div>
        </div>

        {{-- Form Revisi --}}
        <form action="{{ route('layanan.surat.revisi.update', $permohonan->track_token) }}"
            method="POST"
            class="space-y-8"
            enctype="multipart/form-data">
            @csrf
            @method('POST')
            <input type="hidden" name="jenis_surat_id" value="{{ $service->id }}">
            <input type="hidden" name="kelurahan_id" value="{{ $kelurahan->id }}">

            {{-- Show validation errors if any --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="font-semibold text-red-800 mb-2 text-sm">Terdapat kesalahan pada input berikut:</p>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Dynamic Fields Based on Letter Type (same partials as create) --}}
            @if(View::exists('user.permohonan.types.' . strtolower($service->kode)))
            @include('user.permohonan.types.' . strtolower($service->kode))
            @elseif($service->required_fields && count($service->required_fields) > 0)
            @include('user.permohonan.types.dynamic', ['fields' => $service->required_fields])
            @endif

            {{-- Existing Documents Preview --}}
            @if($permohonan->dokumens->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Berkas Sebelumnya
                    <span class="text-xs font-normal text-gray-400 ml-1">(akan dipertahankan jika tidak diganti)</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($permohonan->dokumens as $dok)
                    @php
                    $ext = strtolower(pathinfo($dok->original_name ?? $dok->file_path, PATHINFO_EXTENSION));
                    $isPdf = $ext === 'pdf';
                    $sizeKb = $dok->file_size ? round($dok->file_size / 1024) . ' KB' : '';
                    @endphp
                    <div class="flex items-start gap-3 p-3 rounded-lg border border-dashed border-gray-200 bg-gray-50">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $isPdf ? 'bg-red-100' : 'bg-blue-100' }}">
                            @if($isPdf)
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $dok->nama_dokumen }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $dok->original_name ?? basename($dok->file_path) }}</p>
                            @if($sizeKb)<p class="text-xs text-gray-400">{{ $sizeKb }}</p>@endif
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            ✓ Ada
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Submit --}}
            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('layanan.surat.tracking.search', ['track_token' => $permohonan->track_token]) }}"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    ← Kembali ke Tracking
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-primary-600 rounded-lg text-white font-bold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transition-transform transform hover:-translate-y-1">
                    Ajukan Ulang Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control {
        border-radius: 0.5rem;
        border-color: #d1d5db;
        background-color: #f9fafb;
        padding: 0.75rem 1rem;
        font-family: inherit;
        font-size: 1rem;
        line-height: 1.5rem;
        min-height: 50px;
        transition: background-color 0.2s, border-color 0.2s;
    }

    .ts-control.focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }

    .ts-wrapper.single .ts-control:after {
        right: 1rem;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectPekerjaan = document.querySelector('.select2-pekerjaan');
        if (selectPekerjaan) {
            new TomSelect(selectPekerjaan, {
                create: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Pilih atau Ketik Pekerjaan Baru"
            });
        }

        // Pada halaman revisi, semua input file TIDAK wajib diisi ulang.
        // Dokumen lama akan dipertahankan otomatis jika tidak ada file baru yang dipilih.
        // Kecualikan input OCR (hidden, hanya trigger kamera/file untuk scan KTP).
        document.querySelectorAll('input[type="file"]:not([x-ref])').forEach(function(input) {
            input.removeAttribute('required');
            const hint = document.createElement('p');
            hint.className = 'mt-1 text-xs text-gray-400 italic';
            hint.textContent = 'Opsional — kosongkan jika tidak ingin mengganti dokumen lama.';
            input.parentNode.appendChild(hint);
        });
    });
</script>
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#2563eb',
        });
    });
</script>
@endif
@endsection