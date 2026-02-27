@extends('layouts.app')

@section('title', 'Kelola FAQ Portal')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">FAQ Portal</h1>
        <p class="text-gray-500">Kelola pertanyaan yang sering diajukan di halaman FAQ portal.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.portal.faq.index') }}" method="GET" class="flex gap-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 block text-sm" placeholder="Cari pertanyaan...">
            </div>
            <select name="kategori" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoriOptions as $val => $label)
                <option value="{{ $val }}" @selected(($filters['kategori'] ?? '' )===$val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(($filters['status'] ?? '' )==='aktif' )>Aktif</option>
                <option value="nonaktif" @selected(($filters['status'] ?? '' )==='nonaktif' )>Non-aktif</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Filter</button>
        </form>
        <a href="{{ route('admin.portal.faq.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-sm flex items-center gap-2 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah FAQ
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
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-8">#</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pertanyaan</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Urutan</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($faqs as $faq)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-900 text-sm">{{ Str::limit($faq->pertanyaan, 80) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ Str::limit(strip_tags($faq->jawaban), 90) }}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-primary-50 text-primary-700">
                        {{ $kategoriOptions[$faq->kategori] ?? $faq->kategori }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $faq->urutan }}</td>
                <td class="px-6 py-4">
                    @if($faq->status === 'aktif')
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                    @else
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Non-aktif</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.portal.faq.edit', $faq->id) }}" class="text-blue-600 hover:text-blue-800 p-1">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('admin.portal.faq.destroy', $faq->id) }}" method="POST" class="delete-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-1">
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
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada FAQ. <a href="{{ route('admin.portal.faq.create') }}" class="text-primary-600 underline">Tambah sekarang</a>.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $faqs->withQueryString()->links() }}</div>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus FAQ ini?',
                text: 'Data tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'px-4 py-2 bg-red-500 text-white rounded-lg font-semibold ml-2',
                    cancelButton: 'px-4 py-2 bg-gray-500 text-white rounded-lg font-semibold'
                }
            }).then(r => {
                if (r.isConfirmed) this.submit();
            });
        });
    });
</script>
@endpush