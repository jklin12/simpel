@extends('layouts.app')

@section('title', 'Template Surat')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Template Surat</h1>
        <p class="text-gray-500">Kelola file template surat yang bisa diunduh masyarakat.</p>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        {{-- Filter Form --}}
        <form action="{{ route('admin.template-surat.index') }}" method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                class="pl-3 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 block text-sm"
                placeholder="Cari judul...">

            <select name="jenis_surat_id" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Jenis Surat</option>
                @foreach($jenisSurats as $js)
                <option value="{{ $js->id }}" @selected(($filters['jenis_surat_id'] ?? '' )==$js->id)>{{ $js->nama }}</option>
                @endforeach
            </select>

            <select name="is_active" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Status</option>
                <option value="1" @selected(($filters['is_active'] ?? '' )==='1' )>Aktif</option>
                <option value="0" @selected(($filters['is_active'] ?? '' )==='0' )>Nonaktif</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm transition">Filter</button>
            <a href="{{ route('admin.template-surat.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">Reset</a>
        </form>

        <a href="{{ route('admin.template-surat.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Template
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
    </svg>
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Surat</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul Template</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">File</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Syarat</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($templates as $template)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-600">
                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-medium">
                        {{ $template->jenisSurat->nama ?? '-' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                    {{ $template->judul }}
                    @if($template->deskripsi)
                    <p class="text-xs text-gray-400 font-normal mt-0.5">{{ Str::limit($template->deskripsi, 50) }}</p>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    <a href="{{ $template->file_url }}" target="_blank"
                        class="flex items-center gap-1.5 text-blue-600 hover:text-blue-800 hover:underline">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-xs truncate max-w-[120px]">{{ $template->file_original_name ?? 'Lihat File' }}</span>
                    </a>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    @if($template->syarat && count($template->syarat) > 0)
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs">
                        {{ count($template->syarat) }} syarat
                    </span>
                    @else
                    <span class="text-gray-400 text-xs">–</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm">
                    @if($template->is_active)
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                    @else
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Nonaktif</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.template-surat.edit', $template->id) }}" class="text-blue-600 hover:text-blue-800 p-1" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <form action="{{ route('admin.template-surat.destroy', $template->id) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-1 transition-colors" title="Hapus">
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
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    Belum ada template surat.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $templates->links() }}
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Template?',
                    text: 'Template ini akan dihapus permanen termasuk file-nya!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    buttonsStyling: true,
                }).then(result => {
                    if (result.isConfirmed) this.submit();
                });
            });
        });
    });
</script>
@endpush