@extends('layouts.landing')

@section('title', 'Struktur Organisasi')

@push('head')
<style>
    /* 
  Custom CSS untuk Bagan Organisasi tipe Asimetris/Spesifik (Kecamatan Landasan Ulin)
  Bagan ini dibentuk menggunakan UI Cards modern bersudut melengkung,
  terhubung secara simetris melalui lines custom.
*/
    .org-line-v {
        width: 3px;
        background-color: #0f766e;
        z-index: 5;
    }

    /* Tailwind teal-700 / primary-700 */
    .org-line-h {
        height: 3px;
        background-color: #0f766e;
        z-index: 5;
    }

    .card-border {
        border-color: #e5e7eb;
        border-top-color: #0f766e;
        border-top-width: 4px;
    }

    .card-func {
        border-color: #e5e7eb;
        border-left-color: #0f766e;
        border-left-width: 4px;
    }
</style>
@endpush

@section('content')

<div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white pt-[110px] pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-primary-200 text-sm mb-3">
            <a href="{{ route('portal.home') }}" class="hover:text-white transition">Portal</a>
            <span>/</span>
            <span class="text-white font-medium">Struktur Organisasi</span>
        </nav>
        <h1 class="text-3xl font-extrabold">Struktur Organisasi</h1>
        <p class="text-primary-100 mt-2 text-lg">Bagan kepengurusan Camat, Sekcam, dan Kasi/Kasubag di Kecamatan Landasan Ulin.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-16 w-full overflow-x-auto min-h-[700px] bg-gray-50/30">

    @php
    // Pemetaan manual node berdasar jabatan
    $camat = null; $sekcam = null; $fungsional = null;
    $kasiPem = null; $kasiPMD = null; $kasiTrantib = null; $kasiKesos = null;
    $kasubagUmum = null; $kasubagKeuangan = null;

    $allData = \App\Models\PortalStrukturOrganisasi::orderBy('urutan')->get();
    foreach($allData as $item) {
    $jab = strtolower($item->jabatan);
    if(str_contains($jab, 'camat') && !str_contains($jab, 'sekre')) $camat = $item;
    elseif(str_contains($jab, 'sekre') || str_contains($jab, 'sekcam')) $sekcam = $item;
    elseif(str_contains($jab, 'fungsional')) $fungsional = $item;
    elseif(str_contains($jab, 'pemerintahan')) $kasiPem = $item;
    elseif(str_contains($jab, 'pemberdayaan') || str_contains($jab, 'pmd')) $kasiPMD = $item;
    elseif(str_contains($jab, 'ketentraman') || str_contains($jab, 'trantib')) $kasiTrantib = $item;
    elseif(str_contains($jab, 'kesejahteraan') || str_contains($jab, 'kesos')) $kasiKesos = $item;
    elseif(str_contains($jab, 'umum') && str_contains($jab, 'kepegawaian')) $kasubagUmum = $item;
    elseif(str_contains($jab, 'keuangan') || str_contains($jab, 'pep')) $kasubagKeuangan = $item;
    }
    @endphp

    <div class="w-[1000px] mx-auto flex flex-col items-center relative pb-10">

        {{-- LEVEL 1: CAMAT --}}
        <div class="z-10 bg-white border border-gray-200 card-border rounded-xl p-6 shadow-md flex flex-col items-center justify-center min-w-[320px] max-w-[350px]">
            <h3 class="text-[12px] font-extrabold text-gray-500 uppercase tracking-[0.2em] mb-2 text-center">
                {{ $camat ? $camat->jabatan : 'Camat' }}
            </h3>
            <p class="text-[18px] font-bold text-gray-900 text-center">
                {{ $camat ? $camat->nama : '-' }}
            </p>
        </div>

        {{-- BACKBONE CONTAINER (Spanning from Camat to Seksis) --}}
        <div class="w-full relative flex flex-col items-center">

            {{-- Backbone Line --}}
            <div class="absolute left-1/2 -ml-[1.5px] top-0 bottom-0 org-line-v z-0"></div>

            {{-- Spacing before branches (Level 2) --}}
            <div class="h-[50px] w-full"></div>

            {{-- BRANCHES ROW (Fungsional & Sekretariat) --}}
            <div class="w-full flex z-10">

                {{-- Left Side: Fungsional --}}
                <div class="w-1/2 pr-12 relative flex justify-end items-start pt-4">
                    {{-- Horizontal Link to Backbone --}}
                    <div class="absolute right-0 top-10 w-12 org-line-h z-0"></div>

                    {{-- Fungsional Card --}}
                    <div class="bg-white border border-gray-200 card-func rounded-xl p-5 shadow-sm w-[280px] z-10 relative">
                        <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2 leading-relaxed">
                            {{ $fungsional ? $fungsional->jabatan : 'Kelompok Jabatan Fungsional' }}
                        </h3>
                        <p class="text-[15px] font-bold text-gray-900">
                            {{ $fungsional ? $fungsional->nama : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Right Side: Sekretariat & Subbag --}}
                <div class="w-1/2 pl-12 relative flex justify-start items-start pt-4">
                    {{-- Horizontal Link to Backbone --}}
                    <div class="absolute left-0 top-10 w-12 org-line-h z-0"></div>

                    <div class="flex flex-col items-center z-10 w-full mb-8">
                        {{-- Sekretariat Card --}}
                        <div class="bg-white border border-gray-200 card-border rounded-xl p-5 shadow-sm w-[320px] text-center">
                            <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">
                                {{ $sekcam ? $sekcam->jabatan : 'Sekretariat' }}
                            </h3>
                            <p class="text-[16px] font-bold text-gray-900">
                                {{ $sekcam ? $sekcam->nama : '-' }}
                            </p>
                        </div>

                        {{-- Link to Kasubbag --}}
                        <div class="org-line-v h-[30px]"></div>

                        {{-- Horizontal bridge for Kasubbag --}}
                        <div class="w-[320px] org-line-h relative">
                            <div class="absolute left-0 top-0 org-line-v h-[24px]"></div>
                            <div class="absolute right-0 top-0 org-line-v h-[24px]"></div>
                        </div>

                        {{-- Kasubbag Cards --}}
                        <div class="flex justify-between w-[370px] mt-[24px]">
                            {{-- Kasubbag Umum --}}
                            <div class="bg-white border border-gray-200 card-border rounded-xl p-4 shadow-sm w-[175px] text-center flex flex-col justify-start">
                                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-wide leading-snug mb-3 flex-grow">
                                    {{ $kasubagUmum ? $kasubagUmum->jabatan : 'Sub Bagian Umum & Kepegawaian' }}
                                </h3>
                                <p class="text-[13px] font-semibold text-gray-900 leading-tight">
                                    {{ $kasubagUmum ? $kasubagUmum->nama : '-' }}
                                </p>
                            </div>

                            {{-- Kasubbag PEP --}}
                            <div class="bg-white border border-gray-200 card-border rounded-xl p-4 shadow-sm w-[175px] text-center flex flex-col justify-start">
                                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-wide leading-snug mb-3 flex-grow">
                                    {{ $kasubagKeuangan ? $kasubagKeuangan->jabatan : 'Sub Bagian Keuangan PEP' }}
                                </h3>
                                <p class="text-[13px] font-semibold text-gray-900 leading-tight">
                                    {{ $kasubagKeuangan ? $kasubagKeuangan->nama : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Spacing below branches before Seksis bridge --}}
            <div class="h-[60px] w-full"></div>
        </div>

        {{-- LEVEL 4: 4 SEKSI --}}
        <div class="w-[960px] relative z-10">
            {{-- Horizontal bridge for 4 seksi --}}
            {{-- Backbone vertical line exactly ends at top-0 of this element --}}
            <div class="absolute top-0 left-[110px] right-[110px] org-line-h"></div>

            <div class="flex justify-between w-full pt-0">
                {{-- Seksi 1: Pem --}}
                <div class="flex flex-col items-center w-[210px]">
                    <div class="org-line-v h-[30px]"></div>
                    <div class="bg-white border border-gray-200 card-border rounded-xl p-5 shadow-sm w-full text-center h-full flex flex-col justify-start hover:-translate-y-1 transition duration-300">
                        <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-wide leading-snug mb-3 flex-grow">
                            {{ $kasiPem ? $kasiPem->jabatan : 'Seksi Tata Pemerintahan' }}
                        </h3>
                        <p class="text-[14px] font-semibold text-gray-900 leading-tight">
                            {{ $kasiPem ? $kasiPem->nama : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Seksi 2: PMD --}}
                <div class="flex flex-col items-center w-[210px]">
                    <div class="org-line-v h-[30px]"></div>
                    <div class="bg-white border border-gray-200 card-border rounded-xl p-5 shadow-sm w-full text-center h-full flex flex-col justify-start hover:-translate-y-1 transition duration-300">
                        <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-wide leading-snug mb-3 flex-grow">
                            {{ $kasiPMD ? $kasiPMD->jabatan : 'Seksi Pemberdayaan Masyarakat & Desa' }}
                        </h3>
                        <p class="text-[14px] font-semibold text-gray-900 leading-tight">
                            {{ $kasiPMD ? $kasiPMD->nama : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Seksi 3: Trantib --}}
                <div class="flex flex-col items-center w-[210px]">
                    <div class="org-line-v h-[30px]"></div>
                    <div class="bg-white border border-gray-200 card-border rounded-xl p-5 shadow-sm w-full text-center h-full flex flex-col justify-start hover:-translate-y-1 transition duration-300">
                        <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-wide leading-snug mb-3 flex-grow">
                            {{ $kasiTrantib ? $kasiTrantib->jabatan : 'Seksi Ketentraman & Ketertiban' }}
                        </h3>
                        <p class="text-[14px] font-semibold text-gray-900 leading-tight">
                            {{ $kasiTrantib ? $kasiTrantib->nama : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Seksi 4: Kesos --}}
                <div class="flex flex-col items-center w-[210px]">
                    <div class="org-line-v h-[30px]"></div>
                    <div class="bg-white border border-gray-200 card-border rounded-xl p-5 shadow-sm w-full text-center h-full flex flex-col justify-start hover:-translate-y-1 transition duration-300">
                        <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-wide leading-snug mb-3 flex-grow">
                            {{ $kasiKesos ? $kasiKesos->jabatan : 'Seksi Kesejahteraan Sosial' }}
                        </h3>
                        <p class="text-[14px] font-semibold text-gray-900 leading-tight">
                            {{ $kasiKesos ? $kasiKesos->nama : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection