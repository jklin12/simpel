@extends('layouts.app')

@section('title', 'Daftar Permohonan Surat')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div>
        <nav class="flex items-center gap-2 text-xs font-medium text-gray-400 mb-2 uppercase tracking-wider">
            <span class="text-blue-600">Admin</span>
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Permohonan Surat</span>
        </nav>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Daftar Permohonan</h1>
        <p class="text-gray-500 mt-1">Kelola permohonan layanan surat menyurat secara efisien.</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('layanan.surat.ajukan') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-200 hover:shadow-lg active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Permohonan Baru
        </a>
    </div>
</div>

<!-- Advanced Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <form action="{{ route('admin.permohonan-surat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-4">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5 ml-1">Pencarian</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor, Nama, atau NIK..." class="pl-10 w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-sm transition-all">
            </div>
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5 ml-1">Jenis Surat</label>
            <select name="jenis_surat_id" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-sm transition-all">
                <option value="">Semua Jenis</option>
                @foreach($jenisSurats as $jenisSurat)
                <option value="{{ $jenisSurat->id }}" {{ request('jenis_surat_id') == $jenisSurat->id ? 'selected' : '' }}>
                    {{ $jenisSurat->nama }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5 ml-1">Status</label>
            <select name="status" class="w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-sm transition-all">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>Diproses</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="md:col-span-3 flex items-end gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-gray-900 text-white rounded-xl hover:bg-black transition-all text-sm font-bold shadow-lg shadow-gray-200 active:scale-95">
                Terapkan
            </button>
            <a href="{{ route('admin.permohonan-surat.index') }}" class="p-2.5 text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors" title="Reset Filter">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </form>
</div>

<!-- Premium Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Identitas Surat</th>
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Pemohon</th>
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Wilayah</th>
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Waktu Pengajuan</th>
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($permohonanSurats as $permohonan)
                <tr class="group hover:bg-blue-50/30 transition-all duration-200">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $permohonan->nomor_permohonan }}</div>
                        @if($permohonan->nomor_surat)
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            <span class="text-[11px] font-bold text-green-600 uppercase tracking-tight">{{ $permohonan->nomor_surat }}</span>
                        </div>
                        @else
                        <span class="text-[11px] font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded mt-1 inline-block uppercase italic">Belum Terbit</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $permohonan->nama_pemohon }}</div>
                        <div class="text-[11px] text-gray-400 font-medium flex items-center gap-1 mt-0.5">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H5v-1c0-1.333 2.667-2 4-2z"/></svg>
                            NIK: {{ $permohonan->nik_pemohon }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs font-bold text-gray-800">{{ $permohonan->jenisSurat->nama }}</div>
                        <div class="text-[11px] text-gray-500 mt-1 uppercase tracking-wider font-semibold">Kel. {{ $permohonan->kelurahan->nama }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($permohonan->status == 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-amber-50 text-amber-600 border border-amber-100 shadow-sm shadow-amber-50">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Pending
                        </span>
                        @elseif($permohonan->status == 'approved')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                            Diterima
                        </span>
                        @elseif($permohonan->status == 'completed')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-green-50 text-green-600 border border-green-100">
                            Selesai
                        </span>
                        @elseif($permohonan->status == 'rejected')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-red-50 text-red-600 border border-red-100">
                            Ditolak
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-gray-50 text-gray-600 border border-gray-200">
                            {{ $permohonan->status }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-800">{{ $permohonan->created_at->format('d M Y') }}</div>
                        <div class="text-[10px] font-bold text-gray-400 mt-0.5">{{ $permohonan->created_at->format('H:i') }} WITA</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('admin.permohonan-surat.show', $permohonan->id) }}" class="p-2 text-blue-600 hover:bg-blue-100 rounded-xl transition-all active:scale-95" title="Detail Permohonan">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            @if(Auth::user()->can('delete_permohonan') && $permohonan->status === 'pending')
                            <form action="{{ route('admin.permohonan-surat.destroy', $permohonan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permohonan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-all active:scale-95" title="Hapus Permohonan">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-gray-400 font-semibold uppercase tracking-widest text-xs">Data Tidak Ditemukan</p>
                            <p class="text-sm text-gray-400 mt-1">Belum ada data permohonan surat yang masuk.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($permohonanSurats->hasPages())
    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
        {{ $permohonanSurats->links() }}
    </div>
    @endif
</div>
@endsection
