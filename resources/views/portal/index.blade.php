@extends('layouts.landing')

@section('title', 'Portal Kecamatan Landasan Ulin')

@section('content')

{{-- ===== HERO: DYNAMIC Banner Slider ===== --}}
<section class="relative overflow-hidden pt-[110px]" x-data="{
    current: 0,
    total: {{ $sliders->count() > 0 ? $sliders->count() : 3 }},
    auto: null,
    start() { if (this.total > 1) { this.auto = setInterval(() => this.next(), 5500); } },
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
    <button @click="prev(); stop(); start()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all border border-white/30" aria-label="Sebelumnya">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="next(); stop(); start()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all border border-white/30" aria-label="Berikutnya">
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

@endsection