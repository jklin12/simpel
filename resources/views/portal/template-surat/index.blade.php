@extends('layouts.landing')

@section('title', 'Download Template Surat')

@section('content')
{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-700 to-primary-600 text-white px-4 pt-5 pb-6 lg:pt-12 lg:pb-14">
    <div class="max-w-5xl mx-auto lg:text-center">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold bg-white/15 border border-white/25 rounded-full px-3 py-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" /></svg>
            Download gratis
        </span>
        <h1 class="text-[26px] lg:text-4xl font-bold leading-tight mt-3">Template Surat Pernyataan</h1>
        <p class="text-sm lg:text-lg text-primary-100 mt-1.5 leading-relaxed lg:max-w-2xl lg:mx-auto">
            Unduh template resmi (PDF/DOCX) untuk menyiapkan dokumen Anda sebelum mengajukan.
        </p>
    </div>
</section>

@if($jenisSurats->isEmpty())
<section class="px-4 py-16 text-center">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Template</h3>
    <p class="text-gray-500 text-sm">Template surat belum tersedia saat ini. Silakan cek kembali nanti.</p>
</section>
@else
@php
    $searchIndex = $jenisSurats->map(fn ($j) => Str::lower($j->nama . ' ' . $j->templateSurats->pluck('judul')->implode(' ')))->values();
@endphp
<div x-data="{
        q: '',
        open: {{ $jenisSurats->first()->id }},
        idx: {{ Js::from($searchIndex) }},
        match(i) { const k = this.q.toLowerCase().trim(); return k === '' || this.idx[i].includes(k); },
        get shown() { return this.idx.filter((_, i) => this.match(i)).length; }
    }">

    {{-- Pencarian menempel di bawah navbar --}}
    <div class="sticky top-14 lg:top-16 z-30 bg-gray-50/95 backdrop-blur px-4 py-3 border-b border-gray-200">
        <label class="max-w-5xl mx-auto flex items-center gap-2 h-12 rounded-xl bg-white border border-gray-300 px-3.5 text-gray-400 focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500">
            <svg class="w-5 h-5 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" /></svg>
            <input type="search" x-model.debounce.150ms="q" placeholder="Cari template surat"
                class="flex-1 min-w-0 bg-transparent border-0 p-0 text-[15px] text-gray-900 placeholder-gray-400 focus:ring-0 focus:outline-none">
        </label>
    </div>

    <section class="max-w-5xl mx-auto px-4 mt-4 space-y-2.5">
        @foreach($jenisSurats as $jenisSurat)
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" x-show="match({{ $loop->index }})">

            {{-- Judul jenis surat: teks boleh turun baris, badge dan panah tidak menghimpit --}}
            <button type="button"
                @click="open = (open === {{ $jenisSurat->id }}) ? null : {{ $jenisSurat->id }}"
                :aria-expanded="open === {{ $jenisSurat->id }} || q !== ''"
                class="w-full flex items-center gap-3 px-4 min-h-[64px] py-2 text-left">
                <span class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center flex-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8" /></svg>
                </span>
                <span class="flex-1 min-w-0">
                    <span class="block text-[15px] font-semibold leading-snug text-gray-900">{{ $jenisSurat->nama }}</span>
                    <span class="mt-0.5 inline-block text-[11px] font-semibold text-primary-700 bg-primary-50 rounded-full px-2 py-0.5">{{ $jenisSurat->templateSurats->count() }} template</span>
                </span>
                <svg class="w-5 h-5 text-gray-400 flex-none transition-transform duration-200"
                    :class="(open === {{ $jenisSurat->id }} || q !== '') ? 'rotate-180' : ''"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open === {{ $jenisSurat->id }} || q !== ''" x-cloak
                class="px-3 pb-3 border-t border-gray-100 pt-3 space-y-3">
                @foreach($jenisSurat->templateSurats as $template)
                @php
                    $fileName = $template->file_original_name ?? basename($template->file_path);
                    $ext = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));
                    $sizeLabel = !empty($template->file_size) ? ' · ' . number_format($template->file_size / 1024, 0) . ' KB' : '';
                @endphp
                <div class="rounded-xl border border-gray-200 bg-white p-3.5">
                    <div class="flex gap-3">
                        <span class="w-11 h-11 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center flex-none">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8" /></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[14px] font-semibold leading-snug text-gray-900">{{ $template->judul }}</p>
                            @if($template->deskripsi)
                            <p class="text-[13px] text-gray-500 mt-0.5">{{ $template->deskripsi }}</p>
                            @endif
                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">{{ $fileName }}</p>
                            @if($ext)
                            <p class="text-[11px] text-gray-500 mt-0.5">{{ $ext }}{{ $sizeLabel }}</p>
                            @endif
                        </div>
                    </div>

                    @if($template->syarat && count($template->syarat) > 0)
                    <div x-data="{ s: false }" class="mt-3 rounded-lg bg-gray-50 border border-gray-100">
                        <button type="button" @click="s = !s" :aria-expanded="s" class="w-full flex items-center justify-between h-11 px-3 text-[13px] font-medium text-gray-700">
                            Syarat dokumen ({{ count($template->syarat) }})
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="s ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <ul x-show="s" x-cloak class="px-3 pb-3 space-y-1.5 text-[13px] text-gray-600">
                            @foreach($template->syarat as $syarat)
                            <li class="flex gap-2">
                                <svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                                <span>{{ $syarat }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <a href="{{ route('layanan.surat.template.download', $template->id) }}"
                        class="mt-3 flex items-center justify-center gap-2 h-12 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-[15px] font-semibold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" /></svg>
                        Unduh
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <p x-show="q !== '' && shown === 0" x-cloak class="text-center text-sm text-gray-500 py-10">
            Template tidak ditemukan. Coba kata kunci lain.
        </p>
    </section>

    {{-- Cara menggunakan: langkah ringkas --}}
    <section class="max-w-5xl mx-auto mt-6 px-4">
        <div class="rounded-2xl bg-primary-50 border border-primary-100 p-4">
            <p class="text-[15px] font-semibold text-primary-800">Cara menggunakan template</p>
            <ol class="mt-3 space-y-3">
                @foreach(['Unduh template sesuai jenis surat.', 'Isi data pada template sesuai petunjuk.', 'Bawa dokumen beserta syaratnya ke kantor kelurahan.', 'Atau ajukan secara online lewat menu Ajukan Surat.'] as $i => $step)
                <li class="flex gap-3 items-start">
                    <span class="w-6 h-6 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center flex-none">{{ $i + 1 }}</span>
                    <span class="text-[13px] text-primary-900 leading-relaxed">{{ $step }}</span>
                </li>
                @endforeach
            </ol>
            <a href="{{ route('layanan.index') }}" class="mt-4 flex items-center justify-center gap-2 h-12 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold">
                Ajukan online
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
            </a>
        </div>
    </section>
</div>
@endif
@endsection
