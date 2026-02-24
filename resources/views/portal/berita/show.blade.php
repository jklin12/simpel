@extends('layouts.landing')

@section('title', $berita->judul)

@section('content')

<div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white pt-[110px] pb-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-primary-200 text-sm mb-4 flex-wrap">
            <a href="{{ route('portal.home') }}" class="hover:text-white">Portal</a>
            <span>/</span>
            <a href="{{ route('portal.berita') }}" class="hover:text-white">Berita</a>
            <span>/</span>
            <span class="text-white font-medium">{{ Str::limit($berita->judul, 40) }}</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold leading-snug">{{ $berita->judul }}</h1>
        <p class="text-primary-200 mt-3 text-sm">Dipublikasikan {{ $berita->published_at?->translatedFormat('d F Y') }}</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- Artikel Utama --}}
        <div class="lg:col-span-2">
            @if($berita->thumbnail)
            <div class="rounded-2xl overflow-hidden mb-8 shadow-md">
                <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full object-cover max-h-96">
            </div>
            @endif

            <article class="prose prose-gray max-w-none prose-headings:font-bold prose-a:text-primary-600">
                {!! $berita->konten !!}
            </article>

            <div class="mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('portal.berita') }}" class="inline-flex items-center gap-2 text-primary-600 font-medium hover:text-primary-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke daftar berita
                </a>
            </div>
        </div>

        {{-- Sidebar: Berita Lainnya --}}
        <div class="lg:col-span-1">
            <h3 class="font-bold text-gray-900 mb-5 text-sm uppercase tracking-wider">Berita Lainnya</h3>
            <div class="space-y-4">
                @foreach($beritaLainnya as $item)
                @if($item->id !== $berita->id)
                <a href="{{ route('portal.berita.detail', $item->slug) }}" class="flex gap-3 group">
                    <div class="w-16 h-16 rounded-lg bg-primary-100 overflow-hidden shrink-0">
                        @if($item->thumbnail)
                        <img src="{{ asset('storage/' . $item->thumbnail) }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-primary-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400 mb-0.5">{{ $item->published_at?->format('d M Y') }}</p>
                        <p class="text-sm font-medium text-gray-800 group-hover:text-primary-600 transition-colors line-clamp-2">{{ $item->judul }}</p>
                    </div>
                </a>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection