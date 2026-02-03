@extends('layouts.app')

@section('title', 'Tambah Approval Flow')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.approval-flow.index') }}" class="hover:text-blue-600">Approval Flow</a>
        <span>/</span>
        <span class="text-gray-800">Tambah Baru</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">Tambah Approval Flow Baru</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.approval-flow.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label for="jenis_surat_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat</label>
                <select name="jenis_surat_id" id="jenis_surat_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                    <option value="">Pilih Jenis Surat</option>
                    @foreach($jenisSurats as $jenisSurat)
                    <option value="{{ $jenisSurat->id }}" {{ old('jenis_surat_id') == $jenisSurat->id ? 'selected' : '' }}>
                        {{ $jenisSurat->nama }} ({{ $jenisSurat->kode }})
                    </option>
                    @endforeach
                </select>
                @error('jenis_surat_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kelurahan_id" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                <select name="kelurahan_id" id="kelurahan_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                    <option value="">Pilih Kelurahan</option>
                    @foreach($kelurahans as $kelurahan)
                    <option value="{{ $kelurahan->id }}" {{ old('kelurahan_id') == $kelurahan->id ? 'selected' : '' }}>
                        {{ $kelurahan->nama }} - {{ $kelurahan->kecamatan->nama }}
                    </option>
                    @endforeach
                </select>
                @error('kelurahan_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-gray-200 pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Level Persetujuan</label>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" checked disabled class="rounded border-gray-300 text-blue-600 shadow-sm opacity-50 cursor-not-allowed">
                        </div>
                        <div class="ml-3">
                            <label class="text-sm font-medium text-gray-700">Kelurahan</label>
                            <p class="text-xs text-gray-500">Persetujuan dari admin kelurahan (wajib)</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="require_kecamatan_approval" id="require_kecamatan_approval" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ old('require_kecamatan_approval') ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3">
                            <label for="require_kecamatan_approval" class="text-sm font-medium text-gray-700">Kecamatan</label>
                            <p class="text-xs text-gray-500">Memerlukan persetujuan dari admin kecamatan</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="require_kabupaten_approval" id="require_kabupaten_approval" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ old('require_kabupaten_approval') ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3">
                            <label for="require_kabupaten_approval" class="text-sm font-medium text-gray-700">Kabupaten</label>
                            <p class="text-xs text-gray-500">Memerlukan persetujuan dari admin kabupaten</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Aktifkan approval flow ini
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.approval-flow.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection