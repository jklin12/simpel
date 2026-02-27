@extends('layouts.landing')

@section('title', 'Portal Kecamatan Landasan Ulin')

@section('content')

{{-- ==================== OPSI 3: SEGAR & RAMAH MASYARAKAT ==================== --}}

{{-- Hero: Split screen --}}
<section class="relative overflow-hidden pt-[110px]" style="min-height: 90vh; display: flex; align-items: stretch;">
    {{-- Left half: teal gradient --}}
    <div class="w-full lg:w-1/2 bg-gradient-to-br from-teal-600 to-cyan-700 flex items-center px-8 sm:px-12 lg:px-16 py-20 relative">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url(\" data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg' %3E%3Cg fill='white' fill-rule='evenodd' %3E%3Ccircle cx='20' cy='20' r='2' /%3E%3C/g%3E%3C/svg%3E\");"></div>
        <div class="relative z-10 max-w-lg">
            <span class="inline-flex items-center gap-2 bg-white/20 text-white/90 text-xs font-bold px-4 py-2 rounded-full mb-8 uppercase tracking-widest border border-white/30">
                🌿 Kecamatan Landasan Ulin
            </span>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight mb-5">
                Dekat dengan<br>
                <span class="text-yellow-300">Masyarakat,</span><br>
                Mudah Diakses
            </h1>
            <p class="text-teal-100 text-lg mb-8 leading-relaxed">
                Layanan administrasi, berita terkini, dan informasi wilayah kecamatan — semua ada di satu tempat untuk Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('home') }}" class="px-7 py-4 bg-orange-400 text-white font-bold rounded-2xl hover:bg-orange-300 hover:text-gray-900 transition-all hover:-translate-y-0.5 transform shadow-lg shadow-orange-400/30 text-center text-sm">
                    📝 Ajukan Surat Online
                </a>
                <a href="{{ route('berita.index') }}" class="px-7 py-4 bg-white/15 text-white font-semibold rounded-2xl border border-white/30 hover:bg-white/25 transition-all hover:-translate-y-0.5 transform text-center text-sm">
                    Baca Berita →
                </a>
            </div>
        </div>
    </div>

    {{-- Right half: illustration --}}
    <div class="hidden lg:flex w-1/2 bg-cyan-50 items-center justify-center p-12 relative">
        <div class="absolute inset-0">
            <div class="absolute top-10 right-10 w-40 h-40 bg-orange-100 rounded-full opacity-60"></div>
            <div class="absolute bottom-20 left-10 w-24 h-24 bg-teal-100 rounded-full opacity-80"></div>
        </div>
        <img src="{{ asset('images/hero-illustration.png') }}" alt="Ilustrasi" class="relative z-10 w-full max-w-[460px] h-[420px] rounded-[40px] object-cover shadow-xl border-4 border-white">
    </div>

    {{-- Right half bg on mobile --}}
    <div class="absolute right-0 top-0 bottom-0 w-0 lg:hidden"></div>
</section>

{{-- Services: Pill chips scroll --}}
<section class="bg-white border-y border-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-3 justify-center sm:justify-start">
            <a href="{{route('layanan.index')}}" class="flex items-center gap-2 bg-teal-50 border border-teal-200 text-teal-700 font-semibold text-sm px-5 py-2.5 rounded-full hover:bg-teal-600 hover:text-white hover:border-teal-600 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ajukan Surat
            </a>
            <a href="{{ route('layanan.surat.tracking') }}" class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold text-sm px-5 py-2.5 rounded-full hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Cek Status Surat
            </a>
            <a href="{{ route('peta.index') }}" class="flex items-center gap-2 bg-orange-50 border border-orange-200 text-orange-600 font-semibold text-sm px-5 py-2.5 rounded-full hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                Peta Wilayah
            </a>
            <a href="{{ route('berita.index') }}" class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-600 font-semibold text-sm px-5 py-2.5 rounded-full hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                Baca Berita
            </a>
            <a href="{{ route('struktur-organisasi') }}" class="flex items-center gap-2 bg-purple-50 border border-purple-200 text-purple-600 font-semibold text-sm px-5 py-2.5 rounded-full hover:bg-purple-600 hover:text-white hover:border-purple-600 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Struktur Organisasi
            </a>
        </div>
    </div>
</section>

{{-- Berita Terbaru --}}
<section class="py-20" style="background: linear-gradient(180deg, #f0fdfa 0%, #ffffff 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-teal-600 font-semibold text-xs uppercase tracking-widest mb-2">Informasi Terkini</p>
                <h2 class="text-3xl font-extrabold text-gray-900">Berita &amp; Pengumuman</h2>
            </div>
            <a href="{{ route('berita.index') }}" class="text-teal-600 font-medium hover:text-teal-700 flex items-center gap-1 text-sm">
                Lihat Semua →
            </a>
        </div>
        @if($beritaTerbaru->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($beritaTerbaru as $berita)
            <a href="{{ route('berita.detail', $berita->slug) }}" class="group block bg-white border border-gray-100 rounded-3xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="h-52 bg-gradient-to-br from-teal-100 to-cyan-200 overflow-hidden">
                    @if($berita->thumbnail)
                    <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-teal-300 text-5xl">📰</div>
                    @endif
                </div>
                <div class="p-6">
                    <p class="text-xs text-gray-400 mb-2">{{ $berita->published_at?->format('d M Y') }}</p>
                    <h3 class="font-bold text-gray-900 mb-2 group-hover:text-teal-600 transition-colors line-clamp-2">{{ $berita->judul }}</h3>
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