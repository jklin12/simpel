@extends('layouts.app')

@section('title', 'Edit Kecamatan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.master.kecamatan.index') }}" class="hover:text-blue-600">Master Data</a>
        <span>/</span>
        <a href="{{ route('admin.master.kecamatan.index') }}" class="hover:text-blue-600">Kecamatan</a>
        <span>/</span>
        <span class="text-gray-800">Edit</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">Edit Kecamatan</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.master.kecamatan.update', $kecamatan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label for="kabupaten_id" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                <select name="kabupaten_id" id="kabupaten_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                    <option value="">Pilih Kabupaten</option>
                    @foreach($kabupatens as $kabupaten)
                    <option value="{{ $kabupaten->id }}" {{ old('kabupaten_id', $kecamatan->kabupaten_id) == $kabupaten->id ? 'selected' : '' }}>{{ $kabupaten->nama }}</option>
                    @endforeach
                </select>
                @error('kabupaten_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Kecamatan</label>
                <input type="text" name="nama" id="nama" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: Landasan Ulin" value="{{ old('nama', $kecamatan->nama) }}" required>
                @error('nama')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Kecamatan</label>
                <input type="text" name="kode" id="kode" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: 63.72.01" value="{{ old('kode', $kecamatan->kode) }}" required>
                @error('kode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Kode unik untuk identifikasi wilayah (Kemendagri).</p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.master.kecamatan.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection