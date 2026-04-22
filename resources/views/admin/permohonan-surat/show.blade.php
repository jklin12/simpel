@extends('layouts.app')

@section('title', 'Detail Permohonan')

@section('content')
<div class="mb-10">
    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-[#757682] mb-4">
        <a href="{{ route('admin.permohonan-surat.index') }}" class="hover:text-[#00236f] transition-colors">Daftar Permohonan</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
        <span class="text-[#191c1e]">Detail Permohonan</span>
    </div>
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-extrabold text-[#191c1e] tracking-tight leading-tight mb-2">Detail Permohonan</h1>
            <div class="flex items-center gap-3">
                <span class="text-lg font-medium text-[#444651]">{{ $permohonanSurat->nomor_permohonan }}</span>
                @if(($permohonanSurat->revision_count ?? 0) > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#ffdbcb] text-[#773205]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Revisi Ke-{{ $permohonanSurat->revision_count }}
                </span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if(auth()->user()->hasRole('super_admin') && in_array($permohonanSurat->status, ['approved', 'completed']))
            <form id="form-reset-status" action="{{ route('admin.permohonan-surat.reset-status', $permohonanSurat->id) }}" method="POST">
                @csrf
                <button type="button" onclick="confirmAction('form-reset-status', 'Reset Status?', 'Semua riwayat persetujuan akan diulang kembali menjadi Pending.', 'warning', 'Ya, Reset sekarang')" class="px-5 py-2.5 bg-white text-[#ba1a1a] border border-[#ba1a1a]/20 rounded-xl hover:bg-[#ffdad6] font-bold transition-all flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Reset ke Pending
                </button>
            </form>
            @endif

            @if($permohonanSurat->status == 'pending' || $permohonanSurat->status == 'in_review')
            @php
            $currentApproval = $permohonanSurat->approvals()->where('status', 'pending')->where('step_order', $permohonanSurat->current_step)->first();
            $canApprove = $currentApproval && auth()->user()->hasRole($currentApproval->target_role);
            @endphp

            @if($canApprove)
                <a href="{{ route('admin.permohonan-surat.download', $permohonanSurat->id) }}" target="_blank" class="px-5 py-2.5 bg-white text-[#00236f] border border-[#dce1ff] rounded-xl hover:bg-[#f8f9fb] font-bold transition-all flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Preview PDF
                </a>
                <a href="{{ route('admin.permohonan-surat.edit', $permohonanSurat->id) }}" class="px-5 py-2.5 bg-white text-[#00236f] border border-[#dce1ff] rounded-xl hover:bg-[#f8f9fb] font-bold transition-all flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Edit Data
                </a>
                
                @if(Auth::user()->can('delete_permohonan') && $permohonanSurat->status === 'pending')
                <form id="form-delete-permohonan" action="{{ route('admin.permohonan-surat.destroy', $permohonanSurat->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmAction('form-delete-permohonan', 'Hapus Permohonan?', 'Data yang dihapus tidak dapat dikembalikan.', 'error', 'Ya, Hapus')" class="px-5 py-2.5 bg-white text-[#ba1a1a] border border-[#ffdad6] rounded-xl hover:bg-[#ffdad6] font-bold transition-all flex items-center gap-2 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Hapus
                    </button>
                </form>
                @endif

                <button onclick="showApproveModal()" class="px-6 py-2.5 bg-gradient-to-br from-[#00236f] to-[#1e3a8a] text-white rounded-xl hover:shadow-lg font-bold transition-all">
                    Setujui
                </button>
                <button onclick="showRejectModal()" class="px-6 py-2.5 bg-white text-[#ba1a1a] border border-[#ba1a1a] rounded-xl hover:bg-[#ba1a1a] hover:text-white font-bold transition-all">
                    Tolak
                </button>
            @endif

            @elseif($permohonanSurat->status == 'approved')
            <a href="{{ route('admin.permohonan-surat.download', $permohonanSurat->id) }}" target="_blank"
                class="px-6 py-2.5 bg-gradient-to-br from-[#00236f] to-[#1e3a8a] text-white rounded-xl hover:shadow-lg font-bold transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Download PDF
            </a>
            <button onclick="document.getElementById('upload-signed-panel').classList.toggle('hidden')"
                class="px-6 py-2.5 bg-[#4059aa] text-white rounded-xl hover:shadow-lg font-bold transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                Upload TTD
            </button>

            @elseif($permohonanSurat->status == 'completed')
            @if($permohonanSurat->signed_file_path)
            <a href="{{ Storage::url($permohonanSurat->signed_file_path) }}" target="_blank"
                class="px-6 py-2.5 bg-gradient-to-br from-[#0058be] to-[#2170e4] text-white rounded-xl hover:shadow-lg font-bold transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Download Surat Selesai (TTD)
            </a>
            @else
            <a href="{{ route('admin.permohonan-surat.download', $permohonanSurat->id) }}"
                class="px-6 py-2.5 bg-gradient-to-br from-[#00236f] to-[#1e3a8a] text-white rounded-xl hover:shadow-lg font-bold transition-all flex items-center gap-2">
                Download Draft Surat
            </a>
            @endif
            @endif
        </div>
    </div>
</div>

{{-- Upload TTD Panel (shown when status = approved) --}}
@if($permohonanSurat->status == 'approved')
<div id="upload-signed-panel" class="hidden mb-6">
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-amber-800">Upload Surat yang Sudah Ditandatangani</h3>
                <p class="text-sm text-amber-700 mt-1">
                    1. Download PDF di atas → 2. Tanda tangani secara digital (via aplikasi pihak ketiga) → 3. Upload hasil TTD di sini
                </p>
            </div>
        </div>
        <form action="{{ route('admin.permohonan-surat.upload-signed', $permohonanSurat->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label for="signed_letter" class="block text-sm font-medium text-amber-800 mb-1">File PDF yang sudah di-TTD <span class="text-red-500">*</span></label>
                    <input type="file" name="signed_letter" id="signed_letter" accept=".pdf"
                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 bg-white border border-amber-300 rounded-lg p-1">
                    @error('signed_letter')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-amber-600">Format: PDF, maks. 5MB</p>
                </div>
                <button type="submit" class="px-5 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-medium transition shadow-sm whitespace-nowrap">
                    Upload & Selesaikan
                </button>
            </div>
        </form>
    </div>
</div>
@endif


<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-8 space-y-8">
        <!-- Data Pemohon -->
        <div class="bg-white rounded-[24px] shadow-sm p-8 transition-all hover:shadow-md border border-[#f3f4f6]">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-[#eff6ff] rounded-2xl flex items-center justify-center text-[#1d4ed8]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <h2 class="text-xl font-bold text-[#191c1e] tracking-tight">Data Pemohon</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-1 block">Nama Lengkap</label>
                    <p class="text-lg font-bold text-[#191c1e] leading-tight">{{ $permohonanSurat->nama_pemohon }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-1 block">NIK</label>
                    <p class="text-lg font-bold text-[#191c1e] leading-tight tracking-wider">{{ $permohonanSurat->nik_pemohon }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-1 block">Alamat Tinggal</label>
                    <p class="text-lg text-[#191c1e] leading-relaxed">{{ $permohonanSurat->alamat_pemohon }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-1 block">Nomor Telepon</label>
                    <p class="text-lg text-[#191c1e] font-semibold">{{ $permohonanSurat->phone_pemohon ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-1 block">Kelurahan / Kecamatan</label>
                    <p class="text-lg text-[#191c1e] font-semibold">{{ $permohonanSurat->kelurahan->nama ?? '-' }} / {{ $permohonanSurat->kelurahan->kecamatan->nama ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-1 block">Kode Check</label>
                    <p class="text-lg text-[#191c1e] font-semibold">{{ $permohonanSurat->track_token ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Data Permohonan -->
        <div class="bg-white rounded-[24px] shadow-sm p-8 transition-all hover:shadow-md border border-[#f3f4f6]">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-[#fefcff] rounded-2xl flex items-center justify-center text-[#4059aa]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h2 class="text-xl font-bold text-[#191c1e] tracking-tight">Data Permohonan</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-1 block">Jenis Surat</label>
                    <p class="text-xl font-extrabold text-[#00236f] leading-tight">{{ $permohonanSurat->jenisSurat->nama }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-1 block">Tujuan / Keperluan</label>
                    <p class="text-lg text-[#191c1e] leading-relaxed">{{ $permohonanSurat->keperluan }}</p>
                </div>

                @if($permohonanSurat->data_permohonan)
                <div class="pt-6 border-t border-[#f3f4f6]">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-4 block underline decoration-[#dce1ff] decoration-2 underline-offset-4">Spesifikasi Layanan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
                        @foreach($permohonanSurat->data_permohonan as $key => $value)
                        <div>
                            <label class="text-[9px] font-extrabold uppercase text-[#757682] opacity-70 mb-1 block">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                            <p class="text-base font-bold text-[#191c1e]">{{ $value }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($permohonanSurat->rejected_reason)
                <div class="pt-6 border-t border-[#f3f4f6]">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#ba1a1a] mb-2 block">Alasan Penolakan</label>
                    <div class="bg-[#ffdad6] bg-opacity-30 border border-[#ba1a1a]/10 p-4 rounded-2xl">
                        <p class="text-[#ba1a1a] font-medium leading-relaxed italic">"{{ $permohonanSurat->rejected_reason }}"</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Dokumen Pendukung -->
        @if($permohonanSurat->dokumens->isNotEmpty())
        <div class="bg-white rounded-[24px] shadow-sm p-8 transition-all hover:shadow-md border border-[#f3f4f6]">
            <h2 class="text-xl font-bold text-[#191c1e] mb-8 flex items-center gap-3">
                <div class="w-10 h-10 bg-[#fdeee4] rounded-2xl flex items-center justify-center text-[#4b1c00]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                </div>
                Dokumen Pendukung
                <span class="text-sm font-medium text-[#757682] bg-[#f3f4f6] px-3 py-1 rounded-lg">{{ $permohonanSurat->dokumens->count() }} Files</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($permohonanSurat->dokumens as $dokumen)
                <div class="group flex items-center gap-4 p-4 bg-white rounded-2xl border border-[#f3f4f6] hover:border-[#4059aa]/30 hover:bg-[#edeef0]/30 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors
                        {{ str_contains($dokumen->mime_type ?? '', 'pdf') ? 'bg-[#ffdad6] text-[#ba1a1a]' : 'bg-[#dce1ff] text-[#00236f]' }}">
                        @if(str_contains($dokumen->mime_type ?? '', 'pdf'))
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>
                        @else
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" /></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-[#191c1e] truncate group-hover:text-[#00236f] transition-colors">{{ $dokumen->nama_dokumen }}</p>
                        <p class="text-[11px] font-medium text-[#757682] mt-0.5 truncate uppercase opacity-60">{{ $dokumen->original_name }}</p>
                    </div>
                    <a href="{{ route('admin.permohonan-surat.download-dokumen', [$permohonanSurat->id, $dokumen->id]) }}"
                        class="w-10 h-10 flex items-center justify-center bg-[#f8f9fb] text-[#757682] hover:bg-[#00236f] hover:text-white rounded-xl transition-all" title="Download">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-4 space-y-8">
        <!-- Status Card -->
        <div class="bg-white rounded-[24px] shadow-sm p-8 border border-[#f3f4f6]">
            <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#757682] mb-6 block text-center">Status Progress</h3>
            
            <div class="text-center mb-8">
                @if($permohonanSurat->status == 'pending')
                <div class="inline-flex flex-col items-center">
                    <span class="px-6 py-2.5 text-sm font-extrabold rounded-2xl bg-[#ffdbcb] text-[#773205] shadow-[0_4px_12px_rgba(119,50,5,0.12)]">MENUNGGU VERIFIKASI</span>
                    <p class="text-xs font-semibold text-[#757682] mt-3">Menunggu pengecekan awal admin</p>
                </div>
                @elseif($permohonanSurat->status == 'in_review')
                <div class="inline-flex flex-col items-center">
                    <span class="px-6 py-2.5 text-sm font-extrabold rounded-2xl bg-[#dce1ff] text-[#00236f] shadow-[0_4px_12px_rgba(0,35,111,0.12)]">SEDANG DIPROSES</span>
                    <p class="text-xs font-semibold text-[#757682] mt-3">Sedang direview oleh pejabat terkait</p>
                </div>
                @elseif($permohonanSurat->status == 'approved')
                <div class="inline-flex flex-col items-center">
                    <span class="px-6 py-2.5 text-sm font-extrabold rounded-2xl bg-green-100 text-green-700 shadow-[0_4px_12px_rgba(21,128,61,0.12)]">DISETUJUI</span>
                    <p class="text-xs font-semibold text-[#757682] mt-3">Menunggu upload tanda tangan (TTD)</p>
                </div>
                @elseif($permohonanSurat->status == 'completed')
                <div class="inline-flex flex-col items-center">
                    <span class="px-6 py-2.5 text-sm font-extrabold rounded-2xl bg-[#edeef0] text-[#191c1e] shadow-[0_4px_12px_rgba(0,0,0,0.05)]">SELESAI</span>
                    <p class="text-xs font-semibold text-[#757682] mt-3">Permohonan telah tuntas diproses</p>
                </div>
                @elseif($permohonanSurat->status == 'rejected')
                <div class="inline-flex flex-col items-center">
                    <span class="px-6 py-2.5 text-sm font-extrabold rounded-2xl bg-[#ffdad6] text-[#ba1a1a] shadow-[0_4px_12px_rgba(186,26,26,0.12)]">DITOLAK</span>
                    <p class="text-xs font-semibold text-[#757682] mt-3">Permohonan perlu diperbaiki/direvisi</p>
                </div>
                @endif
            </div>

            @if($permohonanSurat->nomor_surat)
            <div class="pt-6 border-t border-[#f3f4f6]">
                <label class="text-[9px] font-extrabold uppercase text-[#757682] mb-1 block">Nomor Surat Resmi</label>
                <div class="bg-gradient-to-br from-[#f8f9fb] to-[#edeef0] p-4 rounded-2xl border border-[#dce1ff]/50">
                    <p class="text-[17px] font-black text-[#00236f] tracking-tight text-center">{{ $permohonanSurat->nomor_surat }}</p>
                </div>
            </div>
            @endif

            <div class="mt-6 space-y-2">
                <div class="flex items-center justify-between px-1">
                    <span class="text-[10px] font-bold text-[#757682] uppercase">Diajukan Pada</span>
                    <span class="text-[11px] font-bold text-[#191c1e]">{{ $permohonanSurat->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($permohonanSurat->completed_at)
                <div class="flex items-center justify-between px-1">
                    <span class="text-[10px] font-bold text-[#757682] uppercase">Selesai Pada</span>
                    <span class="text-[11px] font-bold text-[#191c1e]">{{ $permohonanSurat->completed_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Approval Timeline -->
        <div class="bg-white rounded-[24px] shadow-sm p-8 border border-[#f3f4f6]">
            <h3 class="text-lg font-bold text-[#191c1e] mb-8 tracking-tight flex items-center gap-2">
                Timeline Persetujuan
                <div class="h-px flex-1 bg-[#edeef0]"></div>
            </h3>
            
            <div class="space-y-0 relative">
                @foreach($approvals as $approval)
                <div class="flex gap-6 pb-8 relative group">
                    @if(!$loop->last)
                    <div class="absolute left-4 top-8 bottom-0 w-px bg-gradient-to-b from-[#edeef0] to-transparent"></div>
                    @endif
                    
                    <div class="relative z-10">
                        @if($approval->status == 'approved')
                        <div class="w-8 h-8 rounded-[10px] bg-[#dce1ff] flex items-center justify-center text-[#00236f] ring-4 ring-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        @elseif($approval->status == 'rejected')
                        <div class="w-8 h-8 rounded-[10px] bg-[#ffdad6] flex items-center justify-center text-[#ba1a1a] ring-4 ring-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        @else
                        <div class="w-8 h-8 rounded-[10px] bg-[#f8f9fb] flex items-center justify-center text-[#c5c5d3] ring-4 ring-white animate-pulse">
                            <div class="w-2 h-2 rounded-full bg-[#c5c5d3]"></div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-0.5">
                            <h4 class="text-sm font-black text-[#191c1e]">{{ $approval->step_name }}</h4>
                        </div>
                        <span class="text-[10px] font-bold text-[#757682] uppercase tracking-wide">{{ ucwords(str_replace('_', ' ', $approval->target_role)) }}</span>
                        
                        @if($approval->status == 'approved')
                        <div class="mt-3 bg-[#f8f9fb] p-3 rounded-2xl border border-[#f3f4f6]">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-5 h-5 rounded-full bg-white flex items-center justify-center text-[10px] shadow-xs">✅</div>
                                <p class="text-[10px] font-bold text-[#191c1e]">{{ $approval->approver ? $approval->approver->name : 'System' }}</p>
                                <span class="text-[9px] text-[#757682] font-semibold ml-auto">{{ $approval->approved_at->format('d/m/y H:i') }}</span>
                            </div>
                            @if($approval->catatan)
                            <p class="text-[11px] text-[#444651] italic leading-relaxed">"{{ $approval->catatan }}"</p>
                            @endif
                        </div>
                        @elseif($approval->status == 'rejected')
                        <div class="mt-3 bg-[#ffdad6]/20 p-3 rounded-2xl border border-[#ba1a1a]/10">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-5 h-5 rounded-full bg-white flex items-center justify-center text-[10px] shadow-xs">❌</div>
                                <p class="text-[10px] font-bold text-[#ba1a1a]">{{ $approval->approver ? $approval->approver->name : 'System' }}</p>
                                <span class="text-[9px] text-[#ba1a1a]/60 font-semibold ml-auto">{{ $approval->approved_at->format('d/m/y H:i') }}</span>
                            </div>
                            @if($approval->catatan)
                            <p class="text-[11px] text-[#ba1a1a] italic leading-relaxed font-medium">"{{ $approval->catatan }}"</p>
                            @endif
                        </div>
                        @elseif($approval->status == 'pending')
                        <div class="mt-2 flex items-center gap-2 px-1">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                            <span class="text-[10px] font-bold text-amber-600 uppercase">Menunggu Antrean</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- WhatsApp Notification Logs -->
        <div class="bg-white rounded-[24px] shadow-sm p-8 border border-[#f3f4f6]">
            <h3 class="text-lg font-bold text-[#191c1e] mb-8 tracking-tight flex items-center gap-2">
                Notifikasi WhatsApp
                <div class="h-px flex-1 bg-[#edeef0]"></div>
            </h3>

            @php
            $whatsappLogs = $permohonanSurat->whatsappLogs()->latest()->get();
            @endphp

            @if($whatsappLogs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#edeef0]">
                                <th class="px-3 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">No</th>
                                <th class="px-3 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Tipe</th>
                                <th class="px-3 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Nomor Tujuan</th>
                                <th class="px-3 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Status</th>
                                <th class="px-3 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Percobaan</th>
                                <th class="px-3 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Waktu</th>
                                <th class="px-3 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#edeef0]">
                            @foreach($whatsappLogs as $index => $log)
                            <tr class="hover:bg-[#fafbfc] transition">
                                <td class="px-3 py-3 text-[12px] font-semibold text-[#191c1e]">{{ $index + 1 }}</td>
                                <td class="px-3 py-3 text-[12px] font-semibold text-[#191c1e]">
                                    @switch($log->notification_type)
                                        @case('created')
                                            <span class="px-2 py-1 rounded-md bg-[#e3f2fd] text-[#1976d2] text-[11px] font-bold">Dibuat</span>
                                            @break
                                        @case('approved')
                                            <span class="px-2 py-1 rounded-md bg-[#e8f5e9] text-[#388e3c] text-[11px] font-bold">Disetujui</span>
                                            @break
                                        @case('rejected')
                                            <span class="px-2 py-1 rounded-md bg-[#ffebee] text-[#d32f2f] text-[11px] font-bold">Ditolak</span>
                                            @break
                                        @case('revisi')
                                            <span class="px-2 py-1 rounded-md bg-[#fff3e0] text-[#f57c00] text-[11px] font-bold">Revisi</span>
                                            @break
                                        @case('sign_request')
                                            <span class="px-2 py-1 rounded-md bg-[#f3e5f5] text-[#7b1fa2] text-[11px] font-bold">TTD</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 rounded-md bg-[#f5f5f5] text-[#616161] text-[11px] font-bold">{{ ucfirst($log->notification_type) }}</span>
                                    @endswitch
                                </td>
                                <td class="px-3 py-3 text-[12px] font-semibold text-[#191c1e] font-mono">{{ $log->phone_to }}</td>
                                <td class="px-3 py-3 text-[12px] font-semibold">
                                    @if($log->status === 'sent')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-[#c8e6c9] text-[#2e7d32]">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Terkirim
                                        </span>
                                    @elseif($log->status === 'failed')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-[#ffcdd2] text-[#c62828]">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                            Gagal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-[#fff9c4] text-[#f57f17]">
                                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-[12px] font-semibold text-[#191c1e] text-center">{{ $log->attempt }}</td>
                                <td class="px-3 py-3 text-[11px] text-[#757682]">{{ $log->updated_at->format('d/m/y H:i') }}</td>
                                <td class="px-3 py-3">
                                    @if($log->status === 'failed')
                                        <form action="{{ route('admin.permohonan-surat.retry-whatsapp', $permohonanSurat->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-bold text-white bg-[#00236f] hover:bg-[#1e3a8a] rounded-lg transition">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                Kirim Ulang
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @if($log->error_message)
                            <tr class="bg-[#fafbfc] border-b border-[#edeef0]">
                                <td colspan="7" class="px-3 py-2">
                                    <div class="text-[11px] text-[#d32f2f] font-medium">
                                        <span class="font-bold">Error:</span> {{ $log->error_message }}
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-[#c5c5d3] mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-[12px] text-[#757682] font-medium">Belum ada notifikasi WhatsApp</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Setujui Permohonan</h3>
        <form action="{{ route('admin.permohonan-surat.approve', $permohonanSurat->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                <textarea name="catatan" id="catatan" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">Setujui</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Tolak Permohonan</h3>
        <form action="{{ route('admin.permohonan-surat.reject', $permohonanSurat->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="rejected_reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="rejected_reason" id="rejected_reason" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Jelaskan alasan penolakan..." required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">Tolak</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function confirmAction(formId, title, text, icon = 'warning', confirmBtnText = 'Ya, Lanjutkan') {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: icon === 'error' ? '#ba1a1a' : '#00236f',
            cancelButtonColor: '#757682',
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[24px]',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function showApproveModal() {
        document.getElementById('approveModal').classList.remove('hidden');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }

    function showRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    document.getElementById('approveModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });

    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>
@endpush
@endsection