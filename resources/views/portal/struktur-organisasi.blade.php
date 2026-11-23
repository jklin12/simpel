@extends('layouts.landing')

@section('title', 'Struktur Organisasi')

@section('content')
<div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white pt-6 pb-6 lg:pt-8 lg:pb-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-primary-200 text-[13px] mb-2" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-white">Portal</a>
            <span>/</span>
            <span class="text-white font-medium">Struktur Organisasi</span>
        </nav>
        <h1 class="text-2xl lg:text-3xl font-extrabold">Struktur Organisasi</h1>
        <p class="text-primary-100 mt-1.5 text-sm lg:text-base">Bagan kepengurusan pemerintahan di Kecamatan Landasan Ulin.</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
    {{-- Pohon vertikal dibangun dari data hierarki (parent_id, urutan), bukan dari pencocokan nama jabatan --}}
    @if($strukturTree->isNotEmpty())
    <ul class="space-y-3">
        @foreach($strukturTree as $node)
        @include('portal.partials.struktur-node-mobile', ['node' => $node, 'depth' => 0])
        @endforeach
    </ul>
    @else
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" /></svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-700 mb-1">Struktur organisasi belum tersedia</h2>
        <p class="text-sm text-gray-500">Data jajaran pejabat kecamatan belum diisi. Silakan cek kembali nanti.</p>
    </div>
    @endif
</div>
@endsection
