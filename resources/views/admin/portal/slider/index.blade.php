@extends('layouts.app')

@section('title', 'Kelola Banner Slider Portal')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Banner Slider Portal</h1>
        <p class="text-gray-500">Kelola slide hero yang ditampilkan di halaman utama portal kecamatan.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.portal.slider.index') }}" method="GET" class="flex gap-2">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(($filters['status'] ?? '' )==='aktif' )>Aktif</option>
                <option value="nonaktif" @selected(($filters['status'] ?? '' )==='nonaktif' )>Non-aktif</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Filter</button>
        </form>
        <a href="{{ route('admin.portal.slider.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-sm flex items-center gap-2 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Slider
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-8">No</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Preview & Judul</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tema</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Urutan</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($sliders as $slider)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if($slider->gambar)
                        <img src="{{ asset('storage/' . $slider->gambar) }}" class="w-20 h-12 rounded-lg object-cover shrink-0 border border-gray-100">
                        @else
                        <div class="w-20 h-12 rounded-lg bg-gradient-to-br
                            @if($slider->warna_tema === 'green') from-emerald-400 to-teal-500
                            @elseif($slider->warna_tema === 'orange') from-orange-400 to-rose-500
                            @else from-blue-500 to-primary-700 @endif
                            shrink-0 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($slider->warna_tema, 0, 3)) }}
                        </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ Str::limit($slider->judul, 60) }}</p>
                            @if($slider->sub_judul)
                            <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($slider->sub_judul, 70) }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        @if($slider->warna_tema === 'green') bg-emerald-100 text-emerald-700
                        @elseif($slider->warna_tema === 'orange') bg-orange-100 text-orange-700
                        @else bg-blue-100 text-blue-700 @endif">
                        {{ \App\Models\PortalSlider::warnaOptions()[$slider->warna_tema] ?? $slider->warna_tema }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $slider->urutan }}</td>
                <td class="px-6 py-4">
                    @if($slider->status === 'aktif')
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                    @else
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Non-aktif</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.portal.slider.edit', $slider->id) }}" class="text-blue-600 hover:text-blue-800 p-1">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('admin.portal.slider.destroy', $slider->id) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Hapus">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada slider. <a href="{{ route('admin.portal.slider.create') }}" class="text-primary-600 underline">Tambah sekarang</a>.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $sliders->withQueryString()->links() }}</div>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus slider ini?',
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