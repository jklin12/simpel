@extends('layouts.landing')

@section('title', 'Download Template Surat')

@section('content')
{{-- Hero Section --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-blue-500 pt-28 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white text-sm font-medium px-4 py-1.5 rounded-full mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Gratis
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Template Surat Pernyataan</h1>
        <p class="text-blue-100 text-lg max-w-2xl mx-auto">
            Unduh template surat resmi dari Kelurahan Landasan Ulin. Tersedia dalam format PDF/DOCX untuk memudahkan persiapan dokumen Anda.
        </p>
    </div>
</section>

{{-- Main Content --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($jenisSurats->isEmpty())
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Template</h3>
            <p class="text-gray-500 text-sm">Template surat belum tersedia saat ini. Silakan cek kembali nanti.</p>
        </div>
        @else

        {{-- Template Cards per Jenis Surat --}}
        <div class="space-y-8" x-data="{ openCategory: {{ $jenisSurats->first()->id }} }">
            @foreach($jenisSurats as $jenisSurat)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Category Header --}}
                <button type="button"
                    @click="openCategory = (openCategory === {{ $jenisSurat->id }}) ? null : {{ $jenisSurat->id }}"
                    class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-800">{{ $jenisSurat->nama }}</h2>
                            @if($jenisSurat->deskripsi)
                            <p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($jenisSurat->deskripsi, 80) }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="px-2.5 py-1 bg-primary-50 text-primary-700 text-xs font-semibold rounded-full">
                            {{ $jenisSurat->templateSurats->count() }} template
                        </span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                            :class="openCategory === {{ $jenisSurat->id }} ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                {{-- Template List --}}
                <div x-show="openCategory === {{ $jenisSurat->id }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1">
                    <div class="border-t border-gray-100 divide-y divide-gray-50">
                        @foreach($jenisSurat->templateSurats as $template)
                        <div class="px-6 py-5">
                            <div class="flex flex-col md:flex-row md:items-start gap-4">
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-800">{{ $template->judul }}</h3>
                                            @if($template->deskripsi)
                                            <p class="text-sm text-gray-500 mt-0.5">{{ $template->deskripsi }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $template->file_original_name ?? basename($template->file_path) }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Syarat-Syarat --}}
                                    @if($template->syarat && count($template->syarat) > 0)
                                    <div class="mt-4 ml-12">
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Syarat Penggunaan</p>
                                        <ul class="space-y-1.5">
                                            @foreach($template->syarat as $syarat)
                                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                                <svg class="w-4 h-4 text-green-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ $syarat }}</span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>

                                {{-- Download Button --}}
                                <div class="shrink-0">
                                    <a href="{{ route('layanan.surat.template.download', $template->id) }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-all shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Info Box --}}
        <div class="mt-10 bg-blue-50 border border-blue-100 rounded-2xl p-6 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-blue-800 mb-1">Cara Menggunakan Template</h4>
                <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                    <li>Download template sesuai jenis surat yang dibutuhkan.</li>
                    <li>Isi data pada template sesuai petunjuk di dalam file.</li>
                    <li>Bawa dokumen yang sudah terisi beserta syarat-syaratnya ke kantor kelurahan.</li>
                    <li>Atau ajukan secara online melalui menu <a href="{{ route('layanan.index') }}" class="font-semibold underline">Buat Surat</a>.</li>
                </ol>
            </div>
        </div>

        @endif
    </div>
</section>
@endsection