@extends('layouts.app')

@section('title', 'Struktur Organisasi')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Struktur Organisasi</h1>
        <p class="text-gray-500">Kelola hierarki dan data pegawai kelurahan/kecamatan.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.portal.struktur-organisasi.index') }}" method="GET" class="flex flex-wrap gap-2" name="filter-form">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500 w-48" placeholder="Cari nama/jabatan...">
            </div>
            <select name="parent_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500 max-w-xs">
                <option value="">Semua Atasan</option>
                @foreach($parents as $p)
                <option value="{{ $p->id }}" @selected(($filters['parent_id'] ?? '' )==$p->id)>{{ $p->nama }} ({{ $p->jabatan }})</option>
                @endforeach
            </select>
        </form>
        <button type="submit" form="filter-form" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Filter</button>
        <a href="{{ route('admin.portal.struktur-organisasi.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-sm flex items-center gap-2 text-sm whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Pejabat
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Profil</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Atasan Langsung</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Urutan</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($data as $item)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                            @if($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover">
                            @else
                            <svg class="w-full h-full text-gray-400 p-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $item->nama }}</p>
                            <p class="text-sm text-primary-600 font-medium">{{ $item->jabatan }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    @if($item->parent)
                    <div class="text-sm">
                        <p class="text-gray-900 font-medium">{{ $item->parent->nama }}</p>
                        <p class="text-gray-500 text-xs">{{ $item->parent->jabatan }}</p>
                    </div>
                    @else
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700 border border-purple-200">Pimpinan Tertinggi</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-gray-100 text-gray-700 border border-gray-200">{{ $item->urutan }}</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.portal.struktur-organisasi.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('admin.portal.struktur-organisasi.destroy', $item->id) }}" method="POST" class="delete-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada data struktur organisasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $data->withQueryString()->links() }}</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Pejabat ini?',
                text: 'Perhatian: Menghapus pejabat ini juga akan menghapus SEMUA bawahannya di struktur bagan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'px-4 py-2 bg-red-500 text-white rounded-lg font-semibold ml-2',
                    cancelButton: 'px-4 py-2 bg-gray-500 text-white rounded-lg font-semibold'
                }
            }).then(result => {
                if (result.isConfirmed) this.submit();
            });
        });
    });
</script>
@endpush