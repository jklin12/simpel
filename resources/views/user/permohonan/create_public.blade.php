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
        <form action="{{ route('permohonan.store.public') }}" method="POST" class="space-y-8" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="jenis_surat_id" value="{{ $service->id }}">
            <input type="hidden" name="kelurahan_id" value="{{ $kelurahan->id }}">

            <!-- Bagian 0: Data Pemohon -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8" x-data="{ nama: '{{ old('pemohon_nama') }}', nik: '{{ old('pemohon_nik') }}' }">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">0</span>
                        Data Pemohon
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" x-model="nama" name="pemohon_nama" value="{{ old('pemohon_nama') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('pemohon_nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                        <input type="text" x-model="nik" name="pemohon_nik" value="{{ old('pemohon_nik') }}" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" maxlength="16" required>
                        @error('pemohon_nik') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="pemohon_alamat" rows="2" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>{{ old('pemohon_alamat') }}</textarea>
                        @error('pemohon_alamat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. WhatsApp / HP</label>
                        <input type="text" name="pemohon_phone" value="{{ old('pemohon_phone') }}" placeholder="Contoh: 08123456789" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4" required>
                        @error('pemohon_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Dynamic Fields Based on Letter Type -->
            @if(View::exists('user.permohonan.types.' . strtolower($service->kode)))
                @include('user.permohonan.types.' . strtolower($service->kode))
            @elseif($service->required_fields && count($service->required_fields) > 0)
                @include('user.permohonan.types.dynamic', ['fields' => $service->required_fields])
            @endif

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <a href="{{ route('services.index') }}" class="mr-4 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Kembali
                </a>
                <button type="submit" class="px-8 py-3 bg-primary-600 rounded-lg text-white font-bold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transition-transform transform hover:-translate-y-1">
                    Kirim Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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