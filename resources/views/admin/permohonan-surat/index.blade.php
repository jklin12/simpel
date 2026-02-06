@extends('layouts.app')

@section('title', 'Daftar Permohonan Surat')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Daftar Permohonan Surat</h1>
        <p class="text-gray-500">Kelola dan proses permohonan surat dari masyarakat.</p>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('permohonan.create.public') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Permohonan Baru
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form action="{{ route('admin.permohonan-surat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor/nama/NIK..." class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        <div>
            <select name="jenis_surat_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua Jenis Surat</option>
                @foreach($jenisSurats as $jenisSurat)
                <option value="{{ $jenisSurat->id }}" {{ request('jenis_surat_id') == $jenisSurat->id ? 'selected' : '' }}>
                    {{ $jenisSurat->nama }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>Sedang Diproses</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm flex-1">
                Filter
            </button>
            <a href="{{ route('admin.permohonan-surat.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Permohonan</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemohon</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Surat</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelurahan</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($permohonanSurats as $permohonan)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                    {{ $permohonan->nomor_permohonan }}
                    @if($permohonan->nomor_surat)
                    <span class="block text-xs text-green-600 font-semibold">{{ $permohonan->nomor_surat }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    <div class="font-medium text-gray-800">{{ $permohonan->nama_pemohon }}</div>
                    <div class="text-xs text-gray-400">NIK: {{ $permohonan->nik_pemohon }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $permohonan->jenisSurat->nama }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $permohonan->kelurahan->nama }}</td>
                <td class="px-6 py-4 text-sm">
                    @if($permohonan->status == 'pending')
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                    @elseif($permohonan->status == 'in_review')
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Sedang Diproses</span>
                    @elseif($permohonan->status == 'completed')
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Selesai</span>
                    @elseif($permohonan->status == 'rejected')
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Ditolak</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $permohonan->created_at->format('d M Y') }}
                    <span class="block text-xs text-gray-400">{{ $permohonan->created_at->format('H:i') }}</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.permohonan-surat.show', $permohonan->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    Belum ada data permohonan surat.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $permohonanSurats->links() }}
</div>
@endsection