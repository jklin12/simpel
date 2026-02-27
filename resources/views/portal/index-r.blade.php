@extends('layouts.landing')

@section('title', 'Portal Kecamatan Landasan Ulin')

@section('content')

{{-- ==================== OPSI R: MODERN CIVIC DASHBOARD STYLE ==================== --}}

<style>
    .civic-gradient {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 40%, #0891b2 100%);
    }

    .feature-hover:hover .feature-icon {
        transform: scale(1.15) rotate(-3deg);
    }

    .feature-icon {
        transition: transform 0.3s ease;
    }
</style>

{{-- Top announcement banner --}}
<div class="bg-yellow-400 text-gray-900 text-sm font-semibold py-2 text-center">
    📢 Layanan pengajuan surat kini tersedia secara online!
    <a href="{{ route('home') }}" class="underline ml-1 hover:text-gray-700">Ajukan sekarang →</a>
</div>

{{-- Hero: Civic Dashboard --}}
<section class="civic-gradient text-white relative overflow-hidden pt-[110px]">
    {{-- Background pattern --}}
    <div class="absolute inset-0 pointer-events-none opacity-10" style="background-image: url(\" data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg' %3E%3Cg fill='none' fill-rule='evenodd' %3E%3Cg fill='white' fill-opacity='1' %3E%3Cpath d='M0 0h40v40H0V0zm40 40h40v40H40V40zm0-40h2l-2 2V0zm0 4l4-4h2l-6 6V4zm0 4l8-8h2L40 10V8zm0 4L52 0h2L40 14v-2zm0 4L56 0h2L40 18v-2zm0 4L60 0h2L40 22v-2zm0 4L64 0h2L40 26v-2zm0 4L68 0h2L40 30v-2zm0 4L72 0h2L40 34v-2zm0 4L76 0h2L40 38v-2zm0 4L80 0v2L42 40h-2zm4 0L80 4v2L46 40h-2zm4 0L80 8v2L50 40h-2zm4 0L80 12v2L54 40h-2zm4 0L80 16v2L58 40h-2zm4 0L80 20v2L62 40h-2zm4 0L80 24v2L66 40h-2zm4 0L80 28v2L70 40h-2zm4 0L80 32v2L74 40h-2zm4 0L80 36v2L78 40h-2zm4 0L80 40zM56 0l24 24v2L54 2V0zm-4 0l24 28v2L50 2V0zm-4 0l24 32v2L46 2V0zm-4 0l24 36v2L42 2V0zm-4 0l24 40h-2L38 2V0zm-4 0l20 40h-2L34 4V0zm-4 0l16 40h-2L30 8V0zm-4 0l12 40h-2L26 12V0zm-4 0l8 40h-2L22 16V0zm-4 0l4 40h-2L18 20V0zm-4 0l0 40h-2L14 24V0z' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Left: Main headline & CTA --}}
            <div class="lg:col-span-6">
                <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 text-white/90 text-xs font-semibold px-4 py-2 rounded-full mb-7 uppercase tracking-widest">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Sistem layanan aktif 24/7
                </div>
                <h1 class="text-5xl lg:text-6xl font-black leading-none mb-5 tracking-tight">
                    Portal<br>
                    <span class="text-cyan-300">Kecamatan</span><br>
                    <span class="text-yellow-300">Landasan Ulin</span>
                </h1>
                <p class="text-blue-100 text-lg mb-9 leading-relaxed max-w-md">
                    Sistem informasi dan pelayanan administrasi terpadu — lebih cepat, lebih transparan, untuk masyarakat Landasan Ulin.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-7 py-4 bg-white text-primary-700 font-black rounded-2xl hover:bg-blue-50 transition-all shadow-xl hover:-translate-y-0.5 text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ajukan Surat
                    </a>
                    <a href="{{ route('layanan.surat.tracking') }}" class="inline-flex items-center gap-2 px-7 py-4 bg-cyan-400/20 border border-cyan-300/40 text-white font-bold rounded-2xl hover:bg-cyan-400/30 transition-all hover:-translate-y-0.5 text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Cek Status
                    </a>
                    <a href="{{ route('peta.index') }}" class="inline-flex items-center gap-2 px-7 py-4 bg-white/10 border border-white/20 text-white font-bold rounded-2xl hover:bg-white/20 transition-all hover:-translate-y-0.5 text-sm">
                        🗺️ Peta Wilayah
                    </a>
                </div>
            </div>

            {{-- Right: Dashboard-style info cards --}}
            <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                {{-- Stat 1 --}}
                <div class="bg-white/10 border border-white/20 backdrop-blur-sm rounded-2xl p-5 hover:bg-white/15 transition-all">
                    <div class="text-3xl mb-2">🏘️</div>
                    <div class="text-3xl font-black text-white">5</div>
                    <div class="text-blue-200 text-sm mt-1">Kelurahan</div>
                </div>
                {{-- Stat 2 --}}
                <div class="bg-white/10 border border-white/20 backdrop-blur-sm rounded-2xl p-5 hover:bg-white/15 transition-all">
                    <div class="text-3xl mb-2">📄</div>
                    <div class="text-3xl font-black text-white">3+</div>
                    <div class="text-blue-200 text-sm mt-1">Jenis Layanan Surat</div>
                </div>
                {{-- Stat 3 --}}
                <div class="bg-yellow-400/20 border border-yellow-300/30 backdrop-blur-sm rounded-2xl p-5 hover:bg-yellow-400/30 transition-all">
                    <div class="text-3xl mb-2">⚡</div>
                    <div class="text-3xl font-black text-yellow-200">24/7</div>
                    <div class="text-yellow-100/80 text-sm mt-1">Akses Online</div>
                </div>
                {{-- Stat 4 --}}
                <div class="bg-cyan-400/20 border border-cyan-300/30 backdrop-blur-sm rounded-2xl p-5 hover:bg-cyan-400/30 transition-all">
                    <div class="text-3xl mb-2">📍</div>
                    <div class="text-3xl font-black text-cyan-200">Real</div>
                    <div class="text-cyan-100/80 text-sm mt-1">Peta Interaktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SVG wave divider --}}
    <div class="relative -mb-px">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none" class="w-full fill-white" style="height:60px;">
            <path d="M0,40L120,36C240,32,480,24,720,28C960,32,1200,48,1320,52L1440,56L1440,60L0,60Z" />
        </svg>
    </div>
</section>

{{-- Feature Cards Row --}}
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="text-primary-600 font-semibold text-xs uppercase tracking-widest mb-2">Apa yang Bisa Anda Lakukan?</p>
            <h2 class="text-3xl font-extrabold text-gray-900">Akses Layanan Kami</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{route('layanan.index')}}" class="feature-hover group relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl p-8 hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 border border-blue-100 hover:border-primary-300">
                <div class="feature-icon w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-primary-200">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-extrabold text-gray-900 text-xl mb-2">Ajukan Surat</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Buat permohonan surat secara online. Tidak perlu datang ke kantor, antrian, atau mengisi formulir kertas.</p>
                <div class="mt-5 text-primary-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">Mulai Sekarang <span>→</span></div>
            </a>
            <a href="{{ route('layanan.surat.tracking') }}" class="feature-hover group relative overflow-hidden bg-gradient-to-br from-green-50 to-emerald-100 rounded-3xl p-8 hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 border border-green-100 hover:border-green-400">
                <div class="feature-icon w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-200">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-extrabold text-gray-900 text-xl mb-2">Cek Status Surat</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Pantau perkembangan permohonan surat Anda secara realtime. Tidak perlu bolak-balik ke kantor.</p>
                <div class="mt-5 text-emerald-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">Lacak Sekarang <span>→</span></div>
            </a>
            <a href="{{ route('peta.index') }}" class="feature-hover group relative overflow-hidden bg-gradient-to-br from-orange-50 to-amber-100 rounded-3xl p-8 hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 border border-orange-100 hover:border-orange-400">
                <div class="feature-icon w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-orange-200">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <h3 class="font-extrabold text-gray-900 text-xl mb-2">Peta Wilayah</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Temukan lokasi Ketua RT, RW, fasilitas kesehatan, pendidikan, tempat ibadah & lebih banyak lagi.</p>
                <div class="mt-5 text-orange-500 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">Buka Peta <span>→</span></div>
            </a>
        </div>
    </div>
</section>

{{-- Berita Terbaru --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-primary-600 font-semibold text-xs uppercase tracking-widest mb-2">Informasi Terkini</p>
                <h2 class="text-3xl font-extrabold text-gray-900">Berita &amp; Pengumuman</h2>
            </div>
            <a href="{{ route('berita.index') }}" class="hidden sm:flex items-center gap-2 text-sm font-semibold text-white bg-primary-600 px-4 py-2 rounded-full hover:bg-primary-700 transition-all">Lihat Semua →</a>
        </div>
        @if($beritaTerbaru->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($beritaTerbaru as $berita)
            <a href="{{ route('berita.detail', $berita->slug) }}" class="group block bg-white border border-gray-100 rounded-3xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 shadow-sm">
                <div class="h-48 bg-gradient-to-br from-primary-100 to-cyan-100 overflow-hidden">
                    @if($berita->thumbnail)
                    <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-primary-300 text-5xl">📰</div>
                    @endif
                </div>
                <div class="p-6">
                    <p class="text-xs text-gray-400 mb-2">{{ $berita->published_at?->format('d M Y') }}</p>
                    <h3 class="font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">{{ $berita->judul }}</h3>
                    <p class="text-gray-500 text-sm line-clamp-2">{!! Str::limit(strip_tags($berita->konten), 110) !!}</p>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 text-gray-400">Belum ada berita yang dipublikasikan.</div>
        @endif
    </div>
</section>

@endsection