@extends('layouts.landing')

@section('title', 'Portal Kecamatan Landasan Ulin')

@section('content')

{{-- ==================== OPSI 2: PREMIUM & DRAMATIS ==================== --}}

<style>
    .hero2-bg {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
</style>

{{-- Hero: full-width, dark navy, text center --}}
<section class="relative hero2-bg text-white overflow-hidden pt-[110px] min-h-[88vh] flex flex-col justify-center">
    {{-- Background grid dots --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle, rgba(255,255,255,0.3) 1px, transparent 1px); background-size: 32px 32px;"></div>

    {{-- Glowing orbs --}}
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/30 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-yellow-400/20 rounded-full blur-[80px] pointer-events-none"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10 text-center">
        <span class="inline-block border border-yellow-400/50 text-yellow-300 text-xs font-semibold px-5 py-2 rounded-full mb-8 uppercase tracking-widest">
            ✦ Portal Resmi Kecamatan Landasan Ulin
        </span>
        <h1 class="text-5xl md:text-7xl font-extrabold leading-tight mb-6">
            Satu Platform<br>
            <span class="text-yellow-400">Semua Layanan</span>
        </h1>
        <p class="text-blue-200 text-xl mb-10 max-w-2xl mx-auto leading-relaxed">
            Akses informasi wilayah, berita terbaru, peta fasilitas, dan pengajuan surat administrasi — semuanya tersedia secara online.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('home') }}" class="px-8 py-4 bg-yellow-400 text-gray-900 font-bold rounded-2xl hover:bg-yellow-300 transition-all hover:-translate-y-1 transform shadow-lg shadow-yellow-400/30 text-sm">
                🗒️ Ajukan Surat Sekarang
            </a>
            <a href="{{ route('portal.berita') }}" class="px-8 py-4 glass-card text-white font-semibold rounded-2xl hover:bg-white/20 transition-all hover:-translate-y-1 transform text-sm">
                📰 Baca Berita
            </a>
        </div>

        {{-- Glassmorphism stat cards --}}
        <div class="mt-20 grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([['5','Kelurahan','🏘️'],['3+','Layanan Surat','📄'],['24/7','Akses Online','⏰'],['Online','Tanpa Antre','⚡']] as [$val,$lab,$ico])
            <div class="glass-card rounded-2xl py-6 px-4">
                <div class="text-3xl mb-1">{{ $ico }}</div>
                <div class="text-2xl font-black text-white">{{ $val }}</div>
                <div class="text-blue-300 text-xs mt-1">{{ $lab }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Bottom wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" class="w-full h-16 md:h-20 fill-white">
            <path d="M0,80L60,66.7C120,53,240,27,360,26.7C480,27,600,53,720,58.7C840,64,960,48,1080,42.7C1200,37,1320,43,1380,45.3L1440,48L1440,80L0,80Z" />
        </svg>
    </div>
</section>

{{-- Services Strip --}}
<section class="bg-white py-12 -mt-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <a href="{{ route('home') }}#buat-surat" class="group relative bg-gradient-to-br from-primary-600 to-primary-800 text-white rounded-2xl p-7 overflow-hidden hover:-translate-y-1 transition-all shadow-lg shadow-primary-200 hover:shadow-xl">
                <div class="absolute -right-5 -bottom-5 w-28 h-28 bg-white/10 rounded-full"></div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-1">Ajukan Surat</h3>
                <p class="text-white/70 text-sm">Online tanpa antre, cepat & transparan</p>
            </a>
            <a href="{{ route('tracking.index') }}" class="group relative bg-gradient-to-br from-emerald-500 to-teal-700 text-white rounded-2xl p-7 overflow-hidden hover:-translate-y-1 transition-all shadow-lg shadow-emerald-200 hover:shadow-xl">
                <div class="absolute -right-5 -bottom-5 w-28 h-28 bg-white/10 rounded-full"></div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-1">Cek Status</h3>
                <p class="text-white/70 text-sm">Lacak permohonan surat secara realtime</p>
            </a>
            <a href="{{ route('portal.peta') }}" class="group relative bg-gradient-to-br from-orange-500 to-rose-600 text-white rounded-2xl p-7 overflow-hidden hover:-translate-y-1 transition-all shadow-lg shadow-orange-200 hover:shadow-xl">
                <div class="absolute -right-5 -bottom-5 w-28 h-28 bg-white/10 rounded-full"></div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-1">Peta Wilayah</h3>
                <p class="text-white/70 text-sm">Temukan lokasi fasilitas & Ketua RT/RW</p>
            </a>
        </div>
    </div>
</section>

{{-- News: Editorial magazine layout --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-primary-600 font-semibold text-xs uppercase tracking-widest mb-2">Informasi Terkini</p>
                <h2 class="text-4xl font-extrabold text-gray-900">Berita &amp; Pengumuman</h2>
            </div>
            <a href="{{ route('portal.berita') }}" class="hidden md:flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 border border-primary-200 px-4 py-2 rounded-full hover:bg-primary-50 transition-all">Lihat Semua →</a>
        </div>
        @if($beritaTerbaru->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            {{-- Featured large card --}}
            @php $first = $beritaTerbaru->first(); @endphp
            <a href="{{ route('portal.berita.detail', $first->slug) }}" class="group md:col-span-3 block bg-white rounded-3xl overflow-hidden hover:shadow-2xl transition-all">
                <div class="h-72 bg-gradient-to-br from-primary-100 to-primary-300 overflow-hidden">
                    @if($first->thumbnail)
                    <img src="{{ asset('storage/' . $first->thumbnail) }}" alt="{{ $first->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-primary-300">
                        <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="p-7">
                    <span class="text-xs bg-primary-100 text-primary-700 font-semibold px-3 py-1 rounded-full">Terbaru</span>
                    <h3 class="font-extrabold text-gray-900 text-xl mt-4 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">{{ $first->judul }}</h3>
                    <p class="text-gray-500 text-sm line-clamp-2">{!! Str::limit(strip_tags($first->konten), 130) !!}</p>
                    <p class="text-xs text-gray-400 mt-4">{{ $first->published_at?->format('d M Y') }}</p>
                </div>
            </a>
            {{-- Smaller cards --}}
            <div class="md:col-span-2 flex flex-col gap-5">
                @foreach($beritaTerbaru->skip(1)->take(2) as $berita)
                <a href="{{ route('portal.berita.detail', $berita->slug) }}" class="group flex gap-4 items-start bg-white rounded-2xl p-4 hover:shadow-lg transition-all">
                    <div class="w-20 h-20 shrink-0 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl overflow-hidden">
                        @if($berita->thumbnail)
                        <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-primary-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-400 mb-1">{{ $berita->published_at?->format('d M Y') }}</p>
                        <h4 class="font-bold text-gray-900 text-sm line-clamp-3 group-hover:text-primary-600 transition-colors">{{ $berita->judul }}</h4>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-20 text-gray-400">Belum ada berita yang dipublikasikan.</div>
        @endif
    </div>
</section>

@endsection