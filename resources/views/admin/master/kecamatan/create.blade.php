@extends('layouts.app')

@section('title', 'Tambah Kecamatan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.master.kecamatan.index') }}" class="hover:text-blue-600">Master Data</a>
        <span>/</span>
        <a href="{{ route('admin.master.kecamatan.index') }}" class="hover:text-blue-600">Kecamatan</a>
        <span>/</span>
        <span class="text-gray-800">Tambah Baru</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">Tambah Kecamatan Baru</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.master.kecamatan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-6">
            <div>
                <label for="kabupaten_id" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                <select name="kabupaten_id" id="kabupaten_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                    <option value="">Pilih Kabupaten</option>
                    @foreach($kabupatens as $kabupaten)
                    <option value="{{ $kabupaten->id }}" {{ old('kabupaten_id') == $kabupaten->id ? 'selected' : '' }}>{{ $kabupaten->nama }}</option>
                    @endforeach
                </select>
                @error('kabupaten_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Kecamatan</label>
                <input type="text" name="nama" id="nama" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: Landasan Ulin" value="{{ old('nama') }}" required>
                @error('nama')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Kecamatan</label>
                <input type="text" name="kode" id="kode" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: 63.72.01" value="{{ old('kode') }}" required>
                @error('kode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Kode unik untuk identifikasi wilayah (Kemendagri).</p>
            </div>

            <hr class="border-gray-200 my-4">

            <h3 class="text-lg font-medium text-gray-900">Data Profil Camat & Kop Surat</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="camat_nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Camat</label>
                    <input type="text" name="camat_nama" id="camat_nama" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: DINNY WAHYUNY, S.STP" value="{{ old('camat_nama') }}">
                </div>
                <div>
                    <label for="camat_nip" class="block text-sm font-medium text-gray-700 mb-1">NIP Camat</label>
                    <input type="text" name="camat_nip" id="camat_nip" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: 19800723 199810 2 001" value="{{ old('camat_nip') }}">
                </div>
                <div>
                    <label for="camat_no_hp" class="block text-sm font-medium text-gray-700 mb-1">No HP Camat (WhatsApp)</label>
                    <input type="text" name="camat_no_hp" id="camat_no_hp" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: 081234567890" value="{{ old('camat_no_hp') }}">
                </div>
                <div>
                    <label for="camat_pangkat" class="block text-sm font-medium text-gray-700 mb-1">Pangkat Camat</label>
                    <input type="text" name="camat_pangkat" id="camat_pangkat" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: Pembina Tingkat I" value="{{ old('camat_pangkat') }}">
                </div>
                <div>
                    <label for="camat_golongan" class="block text-sm font-medium text-gray-700 mb-1">Golongan Camat</label>
                    <input type="text" name="camat_golongan" id="camat_golongan" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: IV/b" value="{{ old('camat_golongan') }}">
                </div>
            </div>

            <div>
                <label for="kop_surat_path" class="block text-sm font-medium text-gray-700 mb-1">Kop Surat Kecamatan (Gambar)</label>
                <input type="file" name="kop_surat_path" id="kop_surat_path" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-xs text-gray-500">Format: JPG/PNG/WEBP, Maks: 2MB. Biasanya berisi logo daerah, nama wilayah, dan alamat lengkap.</p>
                @error('kop_surat_path')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.master.kecamatan.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection