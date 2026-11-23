@extends('layouts.landing')

@section('title', 'Portal Kecamatan Landasan Ulin')

@section('content')

{{-- Tampilan HP/tablet (di bawah lg): desain mobile-first baru --}}
<div class="lg:hidden">
{{-- ===== HERO: slider dinamis (dipendekkan untuk HP, mendukung swipe) ===== --}}
<section class="relative overflow-hidden" x-data="{
    current: 0,
    total: {{ max($sliders->count(), 1) }},
    auto: null,
    sx: 0,
    start() { if (this.total > 1) { this.auto = setInterval(() => this.next(), 10000); } },
    stop() { clearInterval(this.auto); },
    prev() { this.current = (this.current - 1 + this.total) % this.total; },
    next() { this.current = (this.current + 1) % this.total; },
    touchStart(e) { this.sx = e.changedTouches[0].clientX; this.stop(); },
    touchEnd(e) {
        const dx = e.changedTouches[0].clientX - this.sx;
        if (Math.abs(dx) > 40) { dx < 0 ? this.next() : this.prev(); }
        this.start();
    },
}" x-init="start()">

    <div class="overflow-hidden" @touchstart.passive="touchStart($event)" @touchend.passive="touchEnd($event)">
        <div class="flex transition-transform duration-500 ease-in-out" :style="`transform: translateX(-${current * 100}%)`">

            @forelse($sliders as $slider)
            <div class="min-w-full">
                <div class="relative text-white min-h-[320px] lg:min-h-[480px] flex items-center bg-gradient-to-br {{ $slider->gradient }}">
                    @if($slider->gambar)
                    <div class="absolute inset-0 z-0">
                        <img src="{{ asset('storage/' . $slider->gambar) }}" alt="{{ $slider->judul }}"
                            class="w-full h-full object-cover opacity-30" @if(!$loop->first) loading="lazy" @endif>
                        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>
                    </div>
                    @endif

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-12 lg:py-20 w-full relative z-10">
                        <div class="max-w-2xl">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold tracking-wide uppercase bg-white/15 border border-white/25 rounded-full px-3 py-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>Portal Resmi Kecamatan Landasan Ulin
                            </span>
                            <h1 class="text-[27px] sm:text-4xl lg:text-6xl font-bold lg:font-extrabold leading-[1.15] mt-3">{{ $slider->judul }}</h1>
                            @if($slider->sub_judul)
                            <p class="text-sm sm:text-base lg:text-lg text-white/80 mt-2 leading-relaxed line-clamp-3">{{ $slider->sub_judul }}</p>
                            @endif
                            <div class="grid grid-cols-2 sm:flex gap-3 mt-5">
                                @if($slider->label_cta_1 && $slider->url_cta_1)
                                <a href="{{ $slider->url_cta_1 }}" class="flex items-center justify-center h-12 sm:px-7 rounded-xl bg-white text-primary-700 text-[15px] font-semibold hover:bg-primary-50">{{ $slider->label_cta_1 }}</a>
                                @endif
                                @if($slider->label_cta_2 && $slider->url_cta_2)
                                <a href="{{ $slider->url_cta_2 }}" class="flex items-center justify-center h-12 sm:px-7 rounded-xl bg-yellow-400 text-gray-900 text-[15px] font-semibold hover:bg-yellow-300">{{ $slider->label_cta_2 }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="min-w-full">
                <div class="relative bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white min-h-[320px] lg:min-h-[480px] flex items-center">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-12 lg:py-20 relative z-10 w-full">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold tracking-wide uppercase bg-white/15 border border-white/25 rounded-full px-3 py-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>Portal Resmi Kecamatan Landasan Ulin
                        </span>
                        <h1 class="text-[27px] sm:text-4xl lg:text-5xl font-bold leading-[1.15] mt-3">Urus surat kecamatan <span class="text-yellow-400">dari HP Anda</span></h1>
                        <p class="text-sm text-primary-100 mt-2 leading-relaxed">Ajukan, pantau, dan unduh surat tanpa antre.</p>
                        <div class="grid grid-cols-2 sm:flex gap-3 mt-5">
                            <a href="{{ route('layanan.index') }}" class="flex items-center justify-center h-12 sm:px-7 rounded-xl bg-white text-primary-700 text-[15px] font-semibold">Ajukan Surat</a>
                            <a href="{{ route('layanan.surat.tracking') }}" class="flex items-center justify-center h-12 sm:px-7 rounded-xl bg-yellow-400 text-gray-900 text-[15px] font-semibold">Cek Status</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse

        </div>
    </div>

    @if($sliders->count() > 1)
    {{-- Panah (layar lebar) --}}
    <button @click="prev(); stop(); start()" class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full items-center justify-center text-white transition-all border border-white/30" aria-label="Sebelumnya">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
    </button>
    <button @click="next(); stop(); start()" class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full items-center justify-center text-white transition-all border border-white/30" aria-label="Berikutnya">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </button>

    {{-- Penanda halaman (tampil di HP dan layar lebar) --}}
    <div class="absolute bottom-8 lg:bottom-6 left-4 lg:left-1/2 lg:-translate-x-1/2 z-20 flex items-center gap-1.5">
        @for($i = 0; $i < $sliders->count(); $i++)
        <button @click="current = {{ $i }}; stop(); start()" class="rounded-full transition-all duration-300 h-1.5"
            :class="current === {{ $i }} ? 'bg-white w-6' : 'bg-white/40 w-1.5'" aria-label="Slide {{ $i + 1 }}"></button>
        @endfor
    </div>
    @endif
</section>

{{-- Aksi cepat --}}
<section class="-mt-6 mx-4 lg:mx-auto lg:max-w-3xl relative z-10 bg-white rounded-2xl shadow-lg border border-gray-100 p-3 grid grid-cols-4 gap-1">
    @foreach([
        ['Ajukan', route('layanan.index'), 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8'],
        ['Cek Status', route('layanan.surat.tracking'), 'M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'],
        ['Template', route('layanan.surat.template.index'), 'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3'],
        ['Peta', route('peta.index'), 'M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4zM8 2v16M16 6v16'],
    ] as [$label, $href, $iconPath])
    <a href="{{ $href }}" class="flex flex-col items-center gap-1.5 py-2">
        <span class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $iconPath }}" /></svg>
        </span>
        <span class="text-[11px] font-medium text-gray-700">{{ $label }}</span>
    </a>
    @endforeach
</section>

{{-- Cek status cepat --}}
<section class="mx-4 lg:mx-auto lg:max-w-3xl mt-4 bg-white rounded-2xl border border-gray-100 p-4">
    <p class="text-[15px] font-semibold text-gray-900">Sudah punya kode tracking?</p>
    <p class="text-xs text-gray-500 mt-0.5">Masukkan kode untuk melihat status permohonan.</p>
    <form action="{{ route('layanan.surat.tracking.search') }}" method="GET" class="flex gap-2 mt-3">
        <label for="home_track_token" class="sr-only">Kode tracking</label>
        <input id="home_track_token" type="text" name="track_token" placeholder="TRX12345678" maxlength="20" autocomplete="off" autocapitalize="characters" required
            class="flex-1 min-w-0 h-12 rounded-xl bg-gray-50 border-gray-300 focus:ring-primary-500 focus:border-primary-500 uppercase tracking-[0.15em] font-semibold text-sm">
        <button type="submit" class="h-12 px-5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">Cek</button>
    </form>
</section>

{{-- Layanan populer (bulan ini): carousel horizontal --}}
@if($layananPopuler->isNotEmpty())
<section class="mt-7 max-w-7xl mx-auto">
    <div class="flex items-end justify-between px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-[11px] font-semibold tracking-wider text-primary-600 uppercase">Layanan</p>
            <h2 class="text-xl font-bold leading-tight text-gray-900">Surat yang sering diajukan</h2>
        </div>
        <a href="{{ route('layanan.index') }}" class="text-[13px] font-semibold text-primary-600 whitespace-nowrap pb-0.5">Lihat semua</a>
    </div>
    <div class="flex gap-3 overflow-x-auto snap-x snap-mandatory px-4 sm:px-6 lg:px-8 mt-3 pb-1" style="scrollbar-width: none;">
        @foreach($layananPopuler as $item)
        <a href="{{ route('layanan.index') }}" class="snap-start flex-none w-44 bg-white rounded-2xl border border-gray-100 p-4">
            <span class="inline-block text-[10px] font-bold px-2 py-1 rounded-md {{ $item['tone'] }}">{{ $item['kode'] }}</span>
            <p class="text-[15px] font-semibold leading-snug mt-2.5 h-[60px] line-clamp-3 text-gray-900">{{ $item['nama'] }}</p>
            <p class="text-[13px] font-semibold text-primary-600 mt-2 flex items-center gap-1">
                Ajukan
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
            </p>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- Berita terbaru: carousel horizontal di HP, grid di layar lebar --}}
<section class="mt-7 max-w-7xl mx-auto">
    <div class="flex items-end justify-between px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-[11px] font-semibold tracking-wider text-primary-600 uppercase">Informasi Terkini</p>
            <h2 class="text-xl font-bold leading-tight text-gray-900">Berita &amp; Pengumuman</h2>
        </div>
        <a href="{{ route('berita.index') }}" class="text-[13px] font-semibold text-primary-600 whitespace-nowrap pb-0.5">Semua</a>
    </div>
    @if($beritaTerbaru->count() > 0)
    <div class="flex gap-3 overflow-x-auto snap-x snap-mandatory px-4 sm:px-6 lg:px-8 mt-3 pb-1 md:grid md:grid-cols-3 md:overflow-visible" style="scrollbar-width: none;">
        @foreach($beritaTerbaru as $berita)
        <a href="{{ route('berita.detail', $berita->slug) }}" class="snap-start flex-none w-64 md:w-auto bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="h-28 md:h-44 bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center text-primary-200 overflow-hidden">
                @if($berita->thumbnail)
                <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" loading="lazy" class="w-full h-full object-cover">
                @else
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2M18 14h-8M15 18h-5M10 6h8v4h-8z" /></svg>
                @endif
            </div>
            <div class="p-3.5">
                <p class="text-[11px] text-gray-400">{{ $berita->published_at?->format('d M Y') }}</p>
                <p class="text-[14px] font-semibold leading-snug mt-1 line-clamp-3 text-gray-900">{{ $berita->judul }}</p>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <p class="text-center py-10 text-sm text-gray-400">Belum ada berita yang dipublikasikan.</p>
    @endif
</section>

{{-- Teaser struktur organisasi, FAQ, bantuan --}}
<section class="mx-4 lg:mx-auto lg:max-w-3xl mt-7 space-y-3">
    <a href="{{ route('struktur-organisasi') }}" class="flex items-center gap-3 bg-white rounded-2xl border border-gray-100 p-4">
        <span class="w-11 h-11 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center flex-none">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" /></svg>
        </span>
        <span class="flex-1 min-w-0"><span class="block text-[15px] font-semibold text-gray-900">Struktur Organisasi</span><span class="block text-xs text-gray-500">Kenali jajaran pejabat kecamatan</span></span>
        <svg class="w-5 h-5 text-gray-300 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
    </a>
    <a href="{{ route('faq') }}" class="flex items-center gap-3 bg-white rounded-2xl border border-gray-100 p-4">
        <span class="w-11 h-11 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center flex-none">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20zM9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01" /></svg>
        </span>
        <span class="flex-1 min-w-0"><span class="block text-[15px] font-semibold text-gray-900">Pertanyaan Umum (FAQ)</span><span class="block text-xs text-gray-500">Jawaban untuk pertanyaan yang sering diajukan</span></span>
        <svg class="w-5 h-5 text-gray-300 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
    </a>
    @if(isset($bantuanWa) && $bantuanWa)
    <a href="{{ $bantuanWa }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-100 p-4">
        <span class="w-11 h-11 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-none">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.7A8.4 8.4 0 0 1 3.5 11.5 8.5 8.5 0 0 1 12 3a8.4 8.4 0 0 1 9 8.5z" /></svg>
        </span>
        <span class="flex-1 min-w-0"><span class="block text-[15px] font-semibold text-emerald-900">Butuh bantuan?</span><span class="block text-xs text-emerald-700">Hubungi kami lewat WhatsApp</span></span>
    </a>
    @endif
</section>
</div>

{{-- Tampilan desktop (lg ke atas): desain lama dipertahankan --}}
<div class="hidden lg:block">
{{-- ===== HERO: DYNAMIC Banner Slider ===== --}}
<section class="relative overflow-hidden" x-data="{
    current: 0,
    total: {{ $sliders->count() > 0 ? $sliders->count() : 3 }},
    auto: null,
    start() { if (this.total > 1) { this.auto = setInterval(() => this.next(), 10000); } },
    stop() { clearInterval(this.auto); },
    prev() { this.current = (this.current - 1 + this.total) % this.total; },
    next() { this.current = (this.current + 1) % this.total; },
}" x-init="start()">

    <div class="overflow-hidden">
        <div class="flex transition-transform duration-500 ease-in-out" :style="`transform: translateX(-${current * 100}%)`">

            @forelse($sliders as $slider)
            {{-- Slide: data-driven dari DB --}}
            <div class="min-w-full">
                <div class="relative text-white min-h-[90vh] flex items-center
                    bg-gradient-to-br {{ $slider->gradient }}">
                    {{-- Background image overlay --}}
                    @if($slider->gambar)
                    <div class="absolute inset-0 z-0">
                        <img src="{{ asset('storage/' . $slider->gambar) }}" alt="{{ $slider->judul }}"
                            class="w-full h-full object-cover opacity-30">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>
                    </div>
                    @else
                    {{-- Pattern fallback --}}
                    <div class="absolute inset-0 opacity-10 pointer-events-none"
                        style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
                    </div>
                    @endif

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full relative z-10">
                        <div class="max-w-2xl">
                            <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-bold px-4 py-2 rounded-full mb-8 uppercase tracking-wider border border-white/30 backdrop-blur-sm">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Portal Resmi Kecamatan Landasan Ulin
                            </div>
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-5">
                                {{ $slider->judul }}
                            </h1>
                            @if($slider->sub_judul)
                            <p class="text-white/80 text-lg mb-8 leading-relaxed">{{ $slider->sub_judul }}</p>
                            @endif
                            <div class="flex flex-wrap gap-4">
                                @if($slider->label_cta_1 && $slider->url_cta_1)
                                <a href="{{ $slider->url_cta_1 }}"
                                    class="px-7 py-3.5 bg-white text-primary-700 font-bold rounded-xl hover:bg-primary-50 transition-all shadow-lg hover:-translate-y-0.5 text-sm">
                                    {{ $slider->label_cta_1 }} →
                                </a>
                                @endif
                                @if($slider->label_cta_2 && $slider->url_cta_2)
                                <a href="{{ $slider->url_cta_2 }}"
                                    class="px-7 py-3.5 bg-yellow-400 text-gray-900 font-bold rounded-xl hover:bg-yellow-300 transition-all hover:-translate-y-0.5 text-sm">
                                    {{ $slider->label_cta_2 }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            {{-- Fallback: jika belum ada slider di DB --}}
            <div class="min-w-full">
                <div class="relative bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white min-h-[90vh] flex items-center">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative z-10">
                        <h1 class="text-5xl font-extrabold leading-tight mb-6">Informasi &amp; Layanan<br>Kecamatan Landasan Ulin</h1>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('berita.index') }}" class="px-7 py-3.5 bg-white text-primary-700 font-bold rounded-xl hover:bg-primary-50 transition-all text-sm">Baca Berita →</a>
                            <a href="{{ route('home') }}" class="px-7 py-3.5 bg-yellow-400 text-gray-900 font-bold rounded-xl hover:bg-yellow-300 transition-all text-sm">Ajukan Surat</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse

        </div>
    </div>

    {{-- Nav arrows (only if more than 1 slide) --}}
    @if($sliders->count() > 1)
    <button @click="prev(); stop(); start()" class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full items-center justify-center text-white transition-all border border-white/30" aria-label="Sebelumnya">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="next(); stop(); start()" class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full items-center justify-center text-white transition-all border border-white/30" aria-label="Berikutnya">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    {{-- Dot indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        @for($i = 0; $i < $sliders->count(); $i++)
            <button @click="current = {{ $i }}; stop(); start()" class="rounded-full transition-all duration-300 h-2.5" :class="current === {{ $i }} ? 'bg-white w-7' : 'bg-white/50 w-2.5'" aria-label="Slide {{ $i+1 }}"></button>
            @endfor
    </div>
    @endif
</section>

{{-- Services strip --}}
<section class="bg-primary-700 py-9">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{route('layanan.index')}}" class="flex items-center gap-4 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-2xl px-6 py-4 transition-all">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-base">Ajukan Surat</div>
                    <div class="text-white/70 text-xs">Online tanpa antre</div>
                </div>
            </a>
            <a href="{{ route('layanan.surat.tracking') }}" class="flex items-center gap-4 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-2xl px-6 py-4 transition-all">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-base">Cek Status</div>
                    <div class="text-white/70 text-xs">Lacak permohonan</div>
                </div>
            </a>
            <a href="{{ route('peta.index') }}" class="flex items-center gap-4 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-2xl px-6 py-4 transition-all">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-base">Peta Wilayah</div>
                    <div class="text-white/70 text-xs">Lokasi penting</div>
                </div>
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
</div>

@endsection
