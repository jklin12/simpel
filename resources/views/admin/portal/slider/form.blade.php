@extends('layouts.app')

@section('title', isset($slider) ? 'Edit Slider' : 'Tambah Slider')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($slider) ? 'Edit Slider' : 'Tambah Slider Baru' }}</h1>
        <p class="text-gray-500 text-sm">Isi detail banner yang akan tampil di halaman utama portal.</p>
    </div>
    <a href="{{ route('admin.portal.slider.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-1 text-sm">
        ← Kembali
    </a>
</div>

@if($errors->any())
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('error'))
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
@endif

<form
    action="{{ isset($slider) ? route('admin.portal.slider.update', $slider->id) : route('admin.portal.slider.store') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-6">
    @csrf
    @if(isset($slider))
    @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom kiri: konten --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Konten Slider</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul', $slider->judul ?? '') }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Contoh: Informasi & Layanan Kecamatan Landasan Ulin">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sub-Judul / Deskripsi Singkat</label>
                    <input type="text" name="sub_judul" value="{{ old('sub_judul', $slider->sub_judul ?? '') }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Opsional. Deskripsi singkat yang muncul di bawah judul.">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Label Tombol 1</label>
                        <input type="text" name="label_cta_1" value="{{ old('label_cta_1', $slider->label_cta_1 ?? '') }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Contoh: Baca Berita">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Tombol 1</label>
                        <input type="text" name="url_cta_1" value="{{ old('url_cta_1', $slider->url_cta_1 ?? '') }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="/portal/berita">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Label Tombol 2</label>
                        <input type="text" name="label_cta_2" value="{{ old('label_cta_2', $slider->label_cta_2 ?? '') }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Contoh: Ajukan Surat">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Tombol 2</label>
                        <input type="text" name="url_cta_2" value="{{ old('url_cta_2', $slider->url_cta_2 ?? '') }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="/layanan">
                    </div>
                </div>
            </div>

            {{-- Gambar --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3 mb-4">Gambar Banner</h3>
                @if(isset($slider) && $slider->gambar)
                <div class="mb-3">
                    <p class="text-xs text-gray-500 mb-2">Gambar saat ini:</p>
                    <img src="{{ asset('storage/' . $slider->gambar) }}" class="w-full h-40 object-cover rounded-xl border border-gray-100">
                </div>
                @endif
                <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-8 cursor-pointer hover:bg-gray-50 transition-colors">
                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Klik untuk upload gambar</span>
                    <span class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP, maks 3MB. Rasio 16:9 direkomendasikan.</span>
                    <input type="file" name="gambar" accept="image/*" class="hidden" id="gambar-input" onchange="previewImage(this)">
                </label>
                <img id="gambar-preview" class="mt-3 w-full h-40 object-cover rounded-xl hidden border border-gray-100">
            </div>
        </div>

        {{-- Kolom kanan: pengaturan --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Pengaturan</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Warna Tema <span class="text-red-500">*</span></label>
                    <select name="warna_tema" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500">
                        @foreach($warnaOptions as $val => $label)
                        <option value="{{ $val }}" @selected(old('warna_tema', $slider->warna_tema ?? 'blue') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Warna digunakan sebagai fallback jika gambar tidak tersedia.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Urutan Tampil</label>
                    <input type="number" name="urutan" value="{{ old('urutan', $slider->urutan ?? 0) }}" min="0"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="0">
                    <p class="text-xs text-gray-400 mt-1">Angka kecil = tampil lebih awal.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="aktif" @selected(old('status', $slider->status ?? 'aktif') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(old('status', $slider->status ?? 'aktif') === 'nonaktif')>Non-aktif</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all shadow-sm text-sm">
                {{ isset($slider) ? '💾 Simpan Perubahan' : '➕ Tambah Slider' }}
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('gambar-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush