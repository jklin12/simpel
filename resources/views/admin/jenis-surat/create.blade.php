@extends('layouts.app')

@section('title', 'Tambah Jenis Surat')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.jenis-surat.index') }}" class="hover:text-blue-600">Jenis Surat</a>
        <span>/</span>
        <span class="text-gray-800">Tambah Baru</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">Tambah Jenis Surat Baru</h1>
</div>

{{-- Flash Messages --}}
@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start gap-3" role="alert">
    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
    </svg>
    <div>
        <p class="font-semibold">Error!</p>
        <p class="text-sm">{{ session('error') }}</p>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
    <p class="font-semibold mb-2">Terdapat kesalahan pada form:</p>
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.jenis-surat.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Jenis Surat</label>
                <input type="text" name="nama" id="nama" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: Surat Keterangan Domisili" value="{{ old('nama') }}" required>
                @error('nama')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Surat</label>
                <input type="text" name="kode" id="kode" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: SKD" value="{{ old('kode') }}" required>
                @error('kode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Kode unik untuk identifikasi jenis surat.</p>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Deskripsi singkat tentang jenis surat ini...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" value="1" name="is_active" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Aktifkan jenis surat ini
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.jenis-surat.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection