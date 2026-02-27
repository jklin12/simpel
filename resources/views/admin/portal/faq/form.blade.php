@extends('layouts.app')

@section('title', isset($faq) ? 'Edit FAQ' : 'Tambah FAQ')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($faq) ? 'Edit FAQ' : 'Tambah FAQ Baru' }}</h1>
        <p class="text-gray-500 text-sm">Pertanyaan dan jawaban yang akan tampil di halaman FAQ portal.</p>
    </div>
    <a href="{{ route('admin.portal.faq.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-1 text-sm">← Kembali</a>
</div>

@if($errors->any())
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ isset($faq) ? route('admin.portal.faq.update', $faq->id) : route('admin.portal.faq.store') }}" method="POST">
    @csrf
    @if(isset($faq)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kiri: konten --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Konten FAQ</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
                    <input type="text" name="pertanyaan" value="{{ old('pertanyaan', $faq->pertanyaan ?? '') }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Tulis pertanyaan yang sering diajukan...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jawaban <span class="text-red-500">*</span></label>
                    <textarea name="jawaban" rows="6"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Tulis jawaban yang jelas dan informatif...">{{ old('jawaban', $faq->jawaban ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Kanan: pengaturan --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Pengaturan</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500">
                        @foreach($kategoriOptions as $val => $label)
                        <option value="{{ $val }}" @selected(old('kategori', $faq->kategori ?? 'umum') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Urutan Tampil</label>
                    <input type="number" name="urutan" value="{{ old('urutan', $faq->urutan ?? 0) }}" min="0"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-400 mt-1">Angka kecil = tampil lebih awal dalam kategorinya.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="aktif" @selected(old('status', $faq->status ?? 'aktif') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(old('status', $faq->status ?? 'aktif') === 'nonaktif')>Non-aktif</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all shadow-sm text-sm">
                {{ isset($faq) ? '💾 Simpan Perubahan' : '➕ Tambah FAQ' }}
            </button>
        </div>
    </div>
</form>
@endsection