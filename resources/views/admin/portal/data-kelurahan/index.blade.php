@extends('layouts.app')

@section('title', 'Kelola Data Kelurahan')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Data Kelurahan</h1>
        @hasanyrole('admin_kecamatan|admin_kabupaten|super_admin')
        <p class="text-gray-500">Kelola data RW, RT, fasilitas, dan lokasi di wilayah kecamatan.</p>
        @else
        <p class="text-gray-500">
            Data untuk kelurahan:
            <span class="font-semibold text-blue-600">{{ Auth::user()->kelurahan?->nama ?? '-' }}</span>
        </p>
        @endhasanyrole
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.portal.data-kelurahan.index') }}" method="GET" class="flex flex-wrap gap-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500 w-40" placeholder="Cari nama...">
            </div>
            <select name="kategori" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoriList as $key => $label)
                <option value="{{ $key }}" @selected(($filters['kategori'] ?? '' )===$key)>{{ $label }}</option>
                @endforeach
            </select>
            @hasanyrole('admin_kecamatan|admin_kabupaten|super_admin')
            {{-- Filter kelurahan hanya untuk kecamatan/super_admin --}}
            <select name="kelurahan_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Kelurahan</option>
                @foreach($kelurahans as $kel)
                <option value="{{ $kel->id }}" @selected(($filters['kelurahan_id'] ?? '' )==$kel->id)>{{ $kel->nama }}</option>
                @endforeach
            </select>
            @endhasanyrole
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Filter</button>
        </form>
        <a href="{{ route('admin.portal.data-kelurahan.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-sm flex items-center gap-2 text-sm whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Data
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelurahan</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Koordinat</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($data as $item)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if($item->foto)
                        <img src="{{ asset('storage/' . $item->foto) }}" class="w-9 h-9 rounded-lg object-cover shrink-0">
                        @else
                        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-lg shrink-0">
                            {{ \App\Models\PortalDataKelurahan::ikonKategori()[$item->kategori] ?? '📍' }}
                        </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $item->nama }}</p>
                            @if($item->alamat)
                            <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($item->alamat, 40) }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-primary-50 text-primary-700">
                        {{ $kategoriList[$item->kategori] ?? $item->kategori }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->kelurahan?->nama ?? '—' }}</td>
                <td class="px-6 py-4 text-sm">
                    @if($item->latitude && $item->longitude)
                    <span class="text-green-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Ada koordinat
                    </span>
                    @else
                    <span class="text-gray-400 text-xs">Tanpa koordinat</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.portal.data-kelurahan.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('admin.portal.data-kelurahan.destroy', $item->id) }}" method="POST" class="delete-form">
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
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada data.</td>
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
                title: 'Hapus data ini?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
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