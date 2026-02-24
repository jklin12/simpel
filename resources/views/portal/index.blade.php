@extends('layouts.landing')

@section('title', 'Portal Kecamatan Landasan Ulin')

@section('content')

{{-- Hero Section --}}
<section class="relative bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white overflow-hidden lg:pt-[110px]">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block bg-white/20 text-white text-sm font-medium px-4 py-1.5 rounded-full mb-6 backdrop-blur-sm">
                    Portal Resmi Kecamatan Landasan Ulin
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6">
                    Informasi & Layanan Kecamatan Landasan Ulin
                </h1>
                <p class="text-primary-100 text-lg mb-8 leading-relaxed">
                    Akses informasi wilayah, berita terbaru, peta sebaran fasilitas, serta layanan administrasi surat menyurat secara online.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('portal.berita') }}" class="px-6 py-3 bg-white text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition-all shadow-lg shadow-primary-900/30 hover:-translate-y-0.5 transform">
                        Baca Berita
                    </a>
                    <a href="{{ route('portal.peta') }}" class="px-6 py-3 bg-primary-500/40 text-white font-semibold rounded-xl hover:bg-primary-500/60 transition-all border border-white/30 backdrop-blur-sm hover:-translate-y-0.5 transform">
                        Lihat Peta Wilayah
                    </a>
                    <a href="{{ route('home') }}" class="px-6 py-3 bg-secondary-400 text-gray-900 font-semibold rounded-xl hover:bg-secondary-500 transition-all shadow-lg hover:-translate-y-0.5 transform">
                        Ajukan Surat
                    </a>
                </div>
            </div>
            <div class="hidden lg:flex justify-end">
                <div class="grid grid-cols-2 gap-4 w-full max-w-sm">
                    @php
                    $stats = [
                    ['label' => 'Kelurahan', 'value' => '5', 'icon' => '🏘️'],
                    ['label' => 'Layanan Surat', 'value' => '3+', 'icon' => '📄'],
                    ['label' => 'Lokasi Terpetakan', 'value' => '—', 'icon' => '📍'],
                    ['label' => 'Berita Terpublikasi', 'value' => '—', 'icon' => '📰'],
                    ];
                    @endphp
                    @foreach($stats as $stat)
                    <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-5 border border-white/20">
                        <div class="text-3xl mb-2">{{ $stat['icon'] }}</div>
                        <div class="text-2xl font-extrabold">{{ $stat['value'] }}</div>
                        <div class="text-primary-200 text-sm mt-1">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Berita Terbaru --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-primary-600 font-semibold text-sm uppercase tracking-wide mb-1">Informasi Terkini</p>
                <h2 class="text-3xl font-extrabold text-gray-900">Berita & Pengumuman</h2>
            </div>
            <a href="{{ route('portal.berita') }}" class="text-primary-600 font-medium hover:text-primary-700 flex items-center gap-1 text-sm">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        @if($beritaTerbaru->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($beritaTerbaru as $berita)
            <a href="{{ route('portal.berita.detail', $berita->slug) }}" class="group block bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="h-48 bg-gradient-to-br from-primary-100 to-primary-200 overflow-hidden">
                    @if($berita->thumbnail)
                    <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-primary-400">
                        <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="p-5">
                    <p class="text-xs text-gray-400 mb-2">{{ $berita->published_at?->format('d M Y') }}</p>
                    <h3 class="font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">{{ $berita->judul }}</h3>
                    <p class="text-gray-500 text-sm line-clamp-3">{!! Str::limit(strip_tags($berita->konten), 120) !!}</p>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <p>Belum ada berita yang dipublikasikan.</p>
        </div>
        @endif
    </div>
</section>

{{-- Layanan Cepat --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="text-primary-600 font-semibold text-sm uppercase tracking-wide mb-1">Apa yang ingin Anda lakukan?</p>
            <h2 class="text-3xl font-extrabold text-gray-900">Akses Layanan</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <a href="{{ route('home') }}#buat-surat" class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-primary-600 transition-colors">
                    <svg class="w-6 h-6 text-primary-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Ajukan Surat</h3>
                <p class="text-gray-500 text-sm">Buat permohonan surat secara online tanpa antre</p>
            </a>
            <a href="{{ route('tracking.index') }}" class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-600 transition-colors">
                    <svg class="w-6 h-6 text-green-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Cek Status Surat</h3>
                <p class="text-gray-500 text-sm">Pantau progress permohonan surat Anda secara realtime</p>
            </a>
            <a href="{{ route('portal.peta') }}" class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all group">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-orange-600 transition-colors">
                    <svg class="w-6 h-6 text-orange-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Peta Wilayah</h3>
                <p class="text-gray-500 text-sm">Temukan fasilitas dan lokasi penting di kecamatan</p>
            </a>
        </div>
    </div>
</section>

@endsection