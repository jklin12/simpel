@extends('layouts.landing')

@section('title', 'Struktur Organisasi')

@push('head')
<style>
    /* 
  Custom CSS untuk Bagan Organisasi tipe Asimetris (Kecamatan Landasan Ulin)
*/
    .org-line {
        background-color: #0d9488;
        /* Tailwind teal-600 */
    }
</style>
@endpush

@section('content')

<div class="bg-gradient-to-r from-teal-700 to-teal-900 text-white pt-[110px] pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-teal-200 text-sm mb-3">
            <a href="{{ route('portal.home') }}" class="hover:text-white transition">Portal</a>
            <span>/</span>
            <span class="text-white font-medium">Struktur Organisasi</span>
        </nav>
        <h1 class="text-3xl font-extrabold">Struktur Organisasi</h1>
        <p class="text-teal-100 mt-2 text-lg">Bagan kepengurusan pemerintahan di Kecamatan Landasan Ulin.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-16 w-full overflow-x-auto min-h-[700px] bg-slate-50/50">

    @php
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

    <div class="w-[1050px] mx-auto bg-transparent pt-4 pb-20 relative font-sans">

        <!-- LEVEL 1: CAMAT -->
        <div class="flex flex-col items-center relative z-10 w-full mb-0">
            <div class="w-[320px] bg-white border border-gray-100 border-t-4 border-t-teal-600 rounded-2xl p-5 shadow-lg text-center transform transition hover:scale-105">
                <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 border-4 border-white shadow-sm overflow-hidden mb-3">
                    @if($camat && $camat->foto)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($camat->foto) }}" class="w-full h-full object-cover">
                    @else
                    <svg class="w-full h-full text-slate-300 p-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    @endif
                </div>
                <h3 class="text-xs font-bold text-teal-700 uppercase tracking-widest">{{ $camat ? $camat->jabatan : 'CAMAT' }}</h3>
                <p class="text-lg font-extrabold text-slate-800 mt-1">{{ $camat ? $camat->nama : '-' }}</p>
            </div>

            <!-- TRUNK LINE 1 -->
            <div class="w-[3px] org-line h-[50px]"></div>
        </div>

        <!-- MIDDLE SECTION (Fungsional & Sekretariat) -->
        <div class="flex justify-center w-full relative">

            <!-- Central Trunk Line Continues -->
            <div class="absolute left-1/2 -ml-[1.5px] top-0 bottom-[-50px] w-[3px] org-line"></div>

            <!-- Fungsional Branch (Left) -->
            <div class="w-1/2 flex justify-end pr-[40px] relative z-10 pt-[20px]">
                <!-- Horizontal Connector -->
                <div class="absolute right-0 top-[60px] w-[40px] h-[3px] org-line"></div>

                <div class="w-[280px] bg-white border border-gray-100 border-l-4 border-l-teal-600 rounded-2xl p-4 shadow-lg flex items-center gap-4 transform transition hover:-translate-x-1">
                    <div class="w-14 h-14 shrink-0 rounded-full bg-slate-100 border-2 border-white shadow-sm overflow-hidden">
                        @if($fungsional && $fungsional->foto)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($fungsional->foto) }}" class="w-full h-full object-cover">
                        @else
                        <svg class="w-full h-full text-slate-300 p-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-[10px] font-bold text-teal-700 uppercase tracking-widest leading-relaxed">{{ $fungsional ? $fungsional->jabatan : 'KELOMPOK JABATAN FUNGSIONAL' }}</h3>
                        <p class="text-[13px] font-bold text-slate-800 mt-0.5">{{ $fungsional ? $fungsional->nama : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Sekretariat Branch (Right) -->
            <div class="w-1/2 flex justify-start pl-[40px] relative z-10">
                <!-- Horizontal Connector -->
                <div class="absolute left-0 top-[40px] w-[40px] h-[3px] org-line"></div>

                <div class="flex flex-col items-center">
                    <div class="w-[320px] bg-white border border-gray-100 border-t-4 border-t-teal-600 rounded-2xl p-5 shadow-lg text-center transform transition hover:translate-x-1">
                        <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 border-4 border-white shadow-sm overflow-hidden mb-3">
                            @if($sekcam && $sekcam->foto)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($sekcam->foto) }}" class="w-full h-full object-cover">
                            @else
                            <svg class="w-full h-full text-slate-300 p-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            @endif
                        </div>
                        <h3 class="text-[11px] font-bold text-teal-700 uppercase tracking-widest">{{ $sekcam ? $sekcam->jabatan : 'SEKRETARIAT' }}</h3>
                        <p class="text-base font-extrabold text-slate-800 mt-1">{{ $sekcam ? $sekcam->nama : '-' }}</p>
                    </div>

                    <!-- Line down from Sekretariat to Subbags -->
                    <div class="w-[3px] h-[30px] org-line"></div>

                    <!-- Horizontal Bridge for Subbags -->
                    <div class="w-[360px] relative">
                        <div class="w-full h-[3px] org-line"></div>
                        <div class="absolute left-0 top-0 w-[3px] h-[20px] org-line"></div>
                        <div class="absolute right-0 top-0 w-[3px] h-[20px] org-line"></div>
                    </div>

                    <!-- Subbags -->
                    <div class="flex justify-between w-[390px] pt-[20px]">
                        <!-- Subbag Umum -->
                        <div class="w-[185px] bg-white border border-gray-100 border-t-4 border-t-teal-600 rounded-xl p-4 shadow-lg text-center hover:shadow-xl transition">
                            <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 border-2 border-white shadow-sm overflow-hidden mb-3">
                                @if($kasubagUmum && $kasubagUmum->foto)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($kasubagUmum->foto) }}" class="w-full h-full object-cover">
                                @else
                                <svg class="w-full h-full text-slate-300 p-1.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                @endif
                            </div>
                            <h3 class="text-[9px] font-bold text-teal-700 uppercase tracking-widest leading-relaxed h-[28px]">{{ $kasubagUmum ? $kasubagUmum->jabatan : 'SUB BAGIAN UMUM & KEPEG.' }}</h3>
                            <p class="text-[12px] font-bold text-slate-800 mt-2">{{ $kasubagUmum ? $kasubagUmum->nama : '-' }}</p>
                        </div>
                        <!-- Subbag PEP -->
                        <div class="w-[185px] bg-white border border-gray-100 border-t-4 border-t-teal-600 rounded-xl p-4 shadow-lg text-center hover:shadow-xl transition">
                            <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 border-2 border-white shadow-sm overflow-hidden mb-3">
                                @if($kasubagKeuangan && $kasubagKeuangan->foto)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($kasubagKeuangan->foto) }}" class="w-full h-full object-cover">
                                @else
                                <svg class="w-full h-full text-slate-300 p-1.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                @endif
                            </div>
                            <h3 class="text-[9px] font-bold text-teal-700 uppercase tracking-widest leading-relaxed h-[28px]">{{ $kasubagKeuangan ? $kasubagKeuangan->jabatan : 'SUB BAGIAN KEUANGAN PEP' }}</h3>
                            <p class="text-[12px] font-bold text-slate-800 mt-2">{{ $kasubagKeuangan ? $kasubagKeuangan->nama : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Space between Middle and Bottom -->
        <div class="h-[60px] w-full"></div>

        <!-- BOTTOM SECTION (4 Seksis) -->
        <div class="w-full relative z-10">
            <!-- The top horizontal line connecting 4 sections -->
            <!-- We draw it from the center of the first box (12.5%) to the center of the last box (87.5%) -->
            <div class="absolute top-0 left-[11.5%] right-[11.5%] h-[3px] org-line"></div>

            <div class="flex justify-between w-full px-[0%]">
                @foreach([
                ['node' => $kasiPem, 'default' => 'SEKSI TATA PEMERINTAHAN'],
                ['node' => $kasiPMD, 'default' => 'SEKSI PEMBERDAYAAN MASYARAKAT & DESA'],
                ['node' => $kasiTrantib, 'default' => 'SEKSI KETENTRAMAN & KETERTIBAN'],
                ['node' => $kasiKesos, 'default' => 'SEKSI KESEJAHTERAAN SOSIAL'],
                ] as $seksi)
                <div class="flex flex-col items-center w-[23%]">
                    <!-- Vertical drop line -->
                    <div class="w-[3px] h-[35px] org-line"></div>

                    <!-- Seksi Card -->
                    <div class="w-full bg-white border border-gray-100 border-t-4 border-t-teal-600 rounded-2xl p-5 shadow-lg text-center flex flex-col items-center h-full hover:-translate-y-2 transition duration-300">
                        <div class="w-14 h-14 rounded-full bg-slate-100 border-2 border-white shadow-sm overflow-hidden mb-3 shrink-0">
                            @if($seksi['node'] && $seksi['node']->foto)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($seksi['node']->foto) }}" class="w-full h-full object-cover">
                            @else
                            <svg class="w-full h-full text-slate-300 p-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            @endif
                        </div>
                        <h3 class="text-[9.5px] font-bold text-teal-700 uppercase tracking-widest leading-relaxed mb-2 flex-grow">
                            {{ $seksi['node'] ? $seksi['node']->jabatan : $seksi['default'] }}
                        </h3>
                        <p class="text-[13px] font-extrabold text-slate-800 leading-tight">
                            {{ $seksi['node'] ? $seksi['node']->nama : '-' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection