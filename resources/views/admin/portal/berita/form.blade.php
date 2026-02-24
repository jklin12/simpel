@extends('layouts.app')

@section('title', isset($berita) ? 'Edit Berita' : 'Tambah Berita')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.portal.berita.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($berita) ? 'Edit Berita' : 'Tambah Berita Baru' }}</h1>
        <p class="text-gray-500 text-sm">{{ isset($berita) ? 'Perbarui konten berita yang sudah ada' : 'Buat artikel atau pengumuman baru' }}</p>
    </div>
</div>

<form action="{{ isset($berita) ? route('admin.portal.berita.update', $berita->id) : route('admin.portal.berita.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($berita)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                {{-- Judul --}}
                <div>
                    <label for="judul" class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Berita <span class="text-red-500">*</span></label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul', $berita->judul ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('judul') border-red-400 @enderror"
                        placeholder="Masukkan judul berita...">
                    @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Konten --}}
                <div>
                    <label for="konten" class="block text-sm font-semibold text-gray-700 mb-1.5">Konten <span class="text-red-500">*</span></label>
                    <textarea id="konten" name="konten" rows="16"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('konten') border-red-400 @enderror"
                        placeholder="Tulis konten berita di sini...">{{ old('konten', $berita->konten ?? '') }}</textarea>
                    @error('konten') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Konten mendukung HTML dasar (h2, h3, p, ul, ol, strong, em, a).</p>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Status & Publish --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 text-sm">Publikasi</h3>
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft" @selected(old('status', $berita->status ?? 'draft') === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $berita->status ?? '') === 'published')>Published</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.portal.berita.index') }}" class="flex-1 text-center px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 transition shadow-sm">
                        {{ isset($berita) ? 'Simpan Perubahan' : 'Tambah Berita' }}
                    </button>
                </div>
            </div>

            {{-- Thumbnail --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ preview: '{{ isset($berita) && $berita->thumbnail ? asset('storage/' . $berita->thumbnail) : '' }}' }">
                <h3 class="font-semibold text-gray-800 text-sm mb-3">Thumbnail</h3>
                @if(isset($berita) && $berita->thumbnail)
                <div class="mb-3 rounded-lg overflow-hidden border border-gray-100">
                    <img src="{{ asset('storage/' . $berita->thumbnail) }}" class="w-full h-40 object-cover" id="current-thumbnail">
                </div>
                <p class="text-xs text-gray-400 mb-3">Upload gambar baru untuk mengganti thumbnail saat ini.</p>
                @endif

                <div class="relative">
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="hidden"
                        @change="preview = URL.createObjectURL($event.target.files[0])">
                    <label for="thumbnail" class="flex flex-col items-center justify-center gap-2 p-6 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-primary-400 transition" x-show="!preview">
                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs text-gray-400">Klik untuk upload gambar</span>
                        <span class="text-xs text-gray-300">JPG, PNG, WEBP — maks. 2 MB</span>
                    </label>
                    <div x-show="preview" class="relative">
                        <img :src="preview" class="w-full h-40 object-cover rounded-xl">
                        <button type="button" @click="preview = ''; document.getElementById('thumbnail').value = ''" class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs">✕</button>
                    </div>
                </div>
                @error('thumbnail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</form>
@endsection