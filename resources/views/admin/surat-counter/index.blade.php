@extends('layouts.app')

@section('title', 'Monitor Counter Surat')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Monitor Counter Surat</h1>
        <p class="text-gray-500">Pantau nomor urut surat per jenis dan kelurahan.</p>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.surat-counter.index') }}" method="GET" class="flex gap-2">
            <select name="jenis_surat_id" class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua Jenis Surat</option>
                @foreach($jenisSurats as $jenisSurat)
                <option value="{{ $jenisSurat->id }}" {{ request('jenis_surat_id') == $jenisSurat->id ? 'selected' : '' }}>
                    {{ $jenisSurat->nama }}
                </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition shadow-sm">
                Filter
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Surat</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelurahan</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Nomor Terakhir</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($suratCounters as $counter)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                    {{ $counter->jenisSurat->nama }}
                    <span class="text-xs text-gray-400 block">{{ $counter->jenisSurat->kode }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $counter->kelurahan->nama }}
                    <span class="text-xs text-gray-400 block">{{ $counter->kelurahan->kecamatan->nama }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ \Carbon\Carbon::create($counter->year, $counter->month)->format('F Y') }}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-700">
                        {{ str_pad($counter->counter, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <form action="{{ route('admin.surat-counter.reset', $counter->id) }}" method="POST" class="inline reset-form">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-orange-600 hover:text-orange-800 text-sm font-medium transition-colors" title="Reset Counter">
                            Reset
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    Belum ada data counter surat.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $suratCounters->links() }}
</div>

<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-medium mb-1">Informasi Counter:</p>
            <ul class="list-disc list-inside space-y-1 text-blue-700">
                <li>Counter akan otomatis bertambah saat permohonan surat disetujui</li>
                <li>Counter direset otomatis setiap bulan baru</li>
                <li>Tombol "Reset" dapat digunakan untuk mereset counter secara manual jika diperlukan</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resetForms = document.querySelectorAll('.reset-form');

        resetForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Reset Counter?',
                    text: 'Counter akan dikembalikan ke 0. Tindakan ini tidak dapat dibatalkan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f97316',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg ml-2',
                        cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endpush