@extends('layouts.app')

@section('title', 'Edit Data Permohonan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.permohonan-surat.index') }}" class="hover:text-blue-600">Daftar Permohonan</a>
        <span>/</span>
        <a href="{{ route('admin.permohonan-surat.show', $permohonanSurat->id) }}" class="hover:text-blue-600">Detail</a>
        <span>/</span>
        <span class="text-gray-800">Edit Data</span>
    </div>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Data Permohonan</h1>
        <p class="text-gray-500">Koreksi data pemohon sebelum melakukan persetujuan</p>
    </div>
</div>

<form action="{{ route('admin.permohonan-surat.update', $permohonanSurat->id) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Data Pemohon (Utama)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon', $permohonanSurat->nama_pemohon) }}"
                    class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 py-2 px-3" required>
                @error('nama_pemohon') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" name="nik_pemohon" value="{{ old('nik_pemohon', $permohonanSurat->nik_pemohon) }}"
                    class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 py-2 px-3" required maxlength="16">
                @error('nik_pemohon') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp / HP</label>
                <input type="text" name="phone_pemohon" value="{{ old('phone_pemohon', $permohonanSurat->phone_pemohon) }}"
                    class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 py-2 px-3" required>
                @error('phone_pemohon') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
                <input type="text" name="keperluan" value="{{ old('keperluan', $permohonanSurat->keperluan) }}"
                    class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 py-2 px-3" required>
                @error('keperluan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat_pemohon" rows="2"
                    class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 py-2 px-3" required>{{ old('alamat_pemohon', $permohonanSurat->alamat_pemohon) }}</textarea>
                @error('alamat_pemohon') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    @if($permohonanSurat->data_permohonan && is_array($permohonanSurat->data_permohonan))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Detail Isian Surat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($permohonanSurat->data_permohonan as $key => $value)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">{{ str_replace('_', ' ', $key) }}</label>
                <input type="text" name="data_permohonan[{{ $key }}]" value="{{ old('data_permohonan.' . $key, $value) }}"
                    class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 py-2 px-3">
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('admin.permohonan-surat.show', $permohonanSurat->id) }}" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition shadow-sm">
            Batal
        </a>
        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">
            Simpan Perubahan
        </button>
    </div>
</form>
@endsection