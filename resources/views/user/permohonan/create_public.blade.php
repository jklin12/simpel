@extends('layouts.landing')

@section('title', 'Form Pengajuan Surat')

@section('content')
<div class="bg-gray-50 min-h-screen pt-24 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600 font-bold text-xl">
                    {{ substr($service->kode, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $service->nama }}</h1>
                    <p class="text-gray-500">{{ $kelurahan->nama }}, Kecamatan {{ $kelurahan->kecamatan->nama ?? 'Landasan Ulin' }}</p>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                <p class="text-sm text-blue-700">
                    <strong>Penting:</strong> Pastikan seluruh data yang diisi sesuai dengan dokumen asli (KTP/KK). Kesalahan data dapat menyebabkan permohonan ditolak.
                </p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('layanan.surat.store') }}" method="POST" class="space-y-8" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="jenis_surat_id" value="{{ $service->id }}">
            <input type="hidden" name="kelurahan_id" value="{{ $kelurahan->id }}">



            <!-- Dynamic Fields Based on Letter Type -->
            @if(View::exists('user.permohonan.types.' . strtolower($service->kode)))
            @include('user.permohonan.types.' . strtolower($service->kode))
            @elseif($service->required_fields && count($service->required_fields) > 0)
            @include('user.permohonan.types.dynamic', ['fields' => $service->required_fields])
            @endif

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <a href="{{ route('layanan.index') }}" class="mr-4 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Kembali
                </a>
                <button type="submit" class="px-8 py-3 bg-primary-600 rounded-lg text-white font-bold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transition-transform transform hover:-translate-y-1">
                    Kirim Permohonan
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
