@extends('layouts.app')

@section('title', isset($person) ? 'Edit Pejabat' : 'Tambah Pejabat')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.portal.struktur-organisasi.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($person) ? 'Edit Pejabat' : 'Tambah Pejabat Baru' }}</h1>
        <p class="text-gray-500 text-sm">Masukan detail profil dan tentukan posisi di bagan organisasi.</p>
    </div>
</div>

<form action="{{ isset($person) ? route('admin.portal.struktur-organisasi.update', $person->id) : route('admin.portal.struktur-organisasi.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($person)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Info Profil --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">Profil Pejabat</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $person->nama ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('nama') border-red-400 @enderror"
                            placeholder="Contoh: Budi Santoso, S.Kom">
                        @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jabatan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $person->jabatan ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('jabatan') border-red-400 @enderror"
                            placeholder="Contoh: Kasi Pemerintahan">
                        @error('jabatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 mt-2">
                    <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">Posisi dalam Bagan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Atasan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Atasan Langsung (Parent)</label>
                            <select name="parent_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('parent_id') border-red-400 @enderror">
                                <option value="">— Tidak Ada (Pimpinan Tertinggi) —</option>
                                @foreach($parents as $p)
                                <option value="{{ $p->id }}" @selected(old('parent_id', $person->parent_id ?? '') == $p->id)>
                                    {{ $p->nama }} — {{ $p->jabatan }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1.5">Pilih pimpinan di atas jabatan ini untuk menggambar garis hirarki bagan.</p>
                            @error('parent_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Urutan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Urutan Tampil <span class="text-red-500">*</span></label>
                            <input type="number" name="urutan" value="{{ old('urutan', $person->urutan ?? 1) }}" min="0"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('urutan') border-red-400 @enderror">
                            <p class="text-xs text-gray-500 mt-1.5">Urutan box ke samping jika memiliki atasan yang sama (1 = Paling Kiri).</p>
                            @error('urutan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Kolom Kanan: Foto & Submit --}}
        <div class="space-y-6 flex flex-col h-full">
            {{-- Foto Profile --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex-1" x-data="{ preview: '{{ isset($person) && $person->foto ? asset('storage/' . $person->foto) : '' }}' }">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Foto Pejabat</h3>

                <div class="flex flex-col items-center justify-center">
                    <div class="relative w-40 h-40 mb-5 group">
                        <div class="w-full h-full rounded-2xl overflow-hidden bg-gray-50 border-2 border-dashed border-gray-300 group-hover:border-primary-400 transition flex items-center justify-center">
                            <template x-if="preview">
                                <img :src="preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!preview">
                                <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </template>
                        </div>
                        <template x-if="preview">
                            <button type="button" @click="preview = ''; document.getElementById('foto').value = ''" class="absolute -top-3 -right-3 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-md transition-colors">✕</button>
                        </template>
                    </div>

                    <label for="foto" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg cursor-pointer transition w-full text-center">
                        Pilih Pas Foto Baru
                    </label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" class="hidden" @change="preview = URL.createObjectURL($event.target.files[0])">

                    <p class="text-xs text-gray-400 mt-3 text-center">Format: JPG/PNG/WEBP.<br>Disarankan rasio 3:4 atau 1:1.</p>
                </div>
                @error('foto') <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.portal.struktur-organisasi.index') }}" class="flex-1 text-center px-4 py-3 border border-gray-300 text-gray-700 bg-white rounded-xl text-sm font-semibold hover:bg-gray-50 transition shadow-sm">Batal</a>
                <button type="submit" class="flex-1 px-4 py-3 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 shadow-sm shadow-primary-500/30 transition">
                    {{ isset($person) ? 'Simpan Perubahan' : 'Simpan Data' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection