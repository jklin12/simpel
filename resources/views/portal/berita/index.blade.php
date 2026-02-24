@extends('layouts.landing')

@section('title', 'Berita & Pengumuman')

@section('content')

{{-- Page Header --}}
<div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white pt-[110px] pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-primary-200 text-sm mb-4">
            <a href="{{ route('portal.home') }}" class="hover:text-white">Portal</a>
            <span>/</span>
            <span class="text-white font-medium">Berita</span>
        </nav>
        <h1 class="text-3xl font-extrabold">Berita & Pengumuman</h1>
        <p class="text-primary-100 mt-2">Informasi terbaru dari Kecamatan Landasan Ulin</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if($beritas->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-10">
        @foreach($beritas as $berita)
        <a href="{{ route('portal.berita.detail', $berita->slug) }}" class="group block bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="h-48 bg-gradient-to-br from-primary-100 to-primary-200 overflow-hidden">
                @if($berita->thumbnail)
                <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="w-full h-full flex items-center justify-center text-primary-300">
                    <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </div>
                @endif
            </div>
            <div class="p-5">
                <p class="text-xs text-gray-400 mb-2">{{ $berita->published_at?->format('d M Y') }}</p>
                <h2 class="font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">{{ $berita->judul }}</h2>
                <p class="text-gray-500 text-sm line-clamp-3">{!! Str::limit(strip_tags($berita->konten), 120) !!}</p>
                <div class="mt-4 flex items-center gap-1 text-primary-600 font-medium text-sm">
                    Baca selengkapnya
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div>{{ $beritas->links() }}</div>
    @else
    <div class="text-center py-24 text-gray-400">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-500 mb-1">Belum Ada Berita</h3>
        <p class="text-sm">Belum ada berita yang dipublikasikan saat ini.</p>
    </div>
    @endif

</div>

@endsection