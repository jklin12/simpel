@extends('layouts.landing')

@section('title', 'Portal Kecamatan Landasan Ulin')

@section('content')

{{-- ==================== OPSI 1 + BANNER SLIDER ==================== --}}

<style>
    .slide-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }

    .slide {
        min-width: 100%;
        position: relative;
    }
</style>

{{-- Hero with Banner Slider --}}
<section class="relative overflow-hidden pt-[110px]" x-data="{
    current: 0,
    total: 3,
    auto: null,
    start() { this.auto = setInterval(() => this.next(), 5000); },
    prev() { this.current = (this.current - 1 + this.total) % this.total; },
    next() { this.current = (this.current + 1) % this.total; },
}" x-init="start()">

    {{-- Slides --}}
    <div class="overflow-hidden">
        <div class="flex transition-transform duration-500 ease-in-out" :style="`transform: translateX(-${current * 100}%)`">

            {{-- Slide 1: Deep Blue --}}
            <div class="min-w-full">
                <div class="relative bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white min-h-[90vh] flex items-center">
                    <div class="absolute inset-0 overflow-hidden opacity-20 pointer-events-none">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
                    </div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-bold px-4 py-2 rounded-full mb-8 uppercase tracking-wider">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Portal Resmi Kecamatan Landasan Ulin
                                </div>
                                <h1 class="text-5xl font-extrabold leading-tight mb-6">
                                    Informasi &amp;<br><span class="text-yellow-300">Layanan</span><br>Kecamatan
                                </h1>
                                <p class="text-blue-100 text-lg mb-8 leading-relaxed max-w-lg">Akses informasi wilayah, berita terbaru, peta sebaran fasilitas, serta layanan administrasi surat menyurat secara online.</p>
                                <div class="flex flex-wrap gap-4">
                                    <a href="{{ route('berita.index') }}" class="px-7 py-3.5 bg-white text-primary-700 font-bold rounded-xl hover:bg-primary-50 transition-all shadow-lg hover:-translate-y-0.5 text-sm">Baca Berita →</a>
                                    <a href="{{ route('home') }}" class="px-7 py-3.5 bg-yellow-400 text-gray-900 font-bold rounded-xl hover:bg-yellow-300 transition-all hover:-translate-y-0.5 text-sm">Ajukan Surat</a>
                                </div>
                            </div>
                            <div class="hidden lg:flex justify-end items-center">
                                <img src="{{ asset('images/hero-illustration.png') }}" alt="Ilustrasi" class="w-full max-w-[420px] h-[380px] rounded-[40px] object-cover shadow-2xl border-4 border-white/10">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 2: Emerald --}}
            <div class="min-w-full">
                <div class="relative bg-gradient-to-br from-emerald-800 via-teal-700 to-cyan-800 text-white min-h-[90vh] flex items-center">
                    <div class="absolute inset-0 pointer-events-none opacity-10" style="background-image: radial-gradient(circle, rgba(255,255,255,0.4) 1px, transparent 1px); background-size: 30px 30px;"></div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full">
                        <div class="max-w-2xl">
                            <span class="inline-block border border-emerald-300/60 text-emerald-100 text-xs font-bold px-4 py-2 rounded-full mb-8 uppercase tracking-widest">🌿 Kecamatan Landasan Ulin</span>
                            <h1 class="text-5xl font-extrabold leading-tight mb-6">Dekat dengan<br><span class="text-yellow-300">Masyarakat,</span><br>Mudah Diakses</h1>
                            <p class="text-emerald-100 text-lg mb-8 leading-relaxed">Layanan cepat untuk warga — pengajuan surat, cek status, dan informasi wilayah tersedia 24 jam.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('layanan.surat.tracking') }}" class="px-7 py-3.5 bg-white text-emerald-800 font-bold rounded-xl hover:bg-green-50 transition-all hover:-translate-y-0.5 text-sm">Cek Status Surat</a>
                                <a href="{{ route('peta.index') }}" class="px-7 py-3.5 bg-white/20 text-white font-bold rounded-xl border border-white/30 hover:bg-white/30 transition-all hover:-translate-y-0.5 text-sm">Lihat Peta →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 3: Orange --}}
            <div class="min-w-full">
                <div class="relative bg-gradient-to-br from-orange-600 via-rose-600 to-pink-700 text-white min-h-[90vh] flex items-center">
                    <div class="absolute inset-0 pointer-events-none">
                        <svg class="absolute bottom-0 w-full opacity-10" viewBox="0 0 1440 200" preserveAspectRatio="none" style="height:120px;">
                            <path fill="white" d="M0,100L80,90C160,80,320,60,480,70C640,80,800,120,960,120C1120,120,1280,80,1360,60L1440,40L1440,200L0,200Z" />
                        </svg>
                    </div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full">
                        <div class="max-w-2xl">
                            <span class="inline-block bg-white/20 text-white text-xs font-bold px-4 py-2 rounded-full mb-8 uppercase tracking-widest">🗺️ Peta Wilayah</span>
                            <h1 class="text-5xl font-extrabold leading-tight mb-6">Temukan Lokasi<br><span class="text-yellow-200">Penting</span><br>di Kecamatan</h1>
                            <p class="text-rose-100 text-lg mb-8 leading-relaxed">Lokasi Ketua RT, RW, fasilitas umum, tempat ibadah, dan lebih dari puluhan titik penting tersedia di peta interaktif kami.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('peta.index') }}" class="px-7 py-3.5 bg-white text-rose-700 font-bold rounded-xl hover:bg-rose-50 transition-all hover:-translate-y-0.5 text-sm">Buka Peta Wilayah →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Navigation arrows --}}
    <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all border border-white/30">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all border border-white/30">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    {{-- Dot indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <template x-for="i in total" :key="i">
            <button @click="current = i - 1" class="w-2.5 h-2.5 rounded-full transition-all" :class="current === i - 1 ? 'bg-white scale-125 w-6' : 'bg-white/50'"></button>
        </template>
    </div>
</section>

{{-- Services: Blue strip --}}
<section class="bg-primary-700 py-9">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
            [route('layanan.surat.ajukan'), 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'Ajukan Surat', 'Online tanpa antre'],
            [route('layanan.surat.tracking'), 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'Cek Status', 'Lacak permohonan'],
            [route('peta.index'), 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7', 'Peta Wilayah', 'Lokasi penting'],
            ] as [$url, $icon, $title, $sub])
            <a href="{{ $url }}" class="flex items-center gap-4 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-2xl px-6 py-4 transition-all">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-base">{{ $title }}</div>
                    <div class="text-white/70 text-xs">{{ $sub }}</div>
                </div>
            </a>
            @endforeach
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
            <a href="{{ route('berita.index') }}" class="text-primary-600 font-medium hover:text-primary-700 text-sm border-b border-primary-600 pb-0.5">Lihat Semua →</a>
        </div>
        @if($beritaTerbaru->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($beritaTerbaru as $berita)
            <a href="{{ route('berita.detail', $berita->slug) }}" class="group block bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="h-48 bg-gradient-to-br from-primary-100 to-primary-200 overflow-hidden">
                    @if($berita->thumbnail)
                    <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-primary-300 text-4xl">📰</div>
                    @endif
                </div>
                <div class="p-5">
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