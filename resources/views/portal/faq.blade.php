@extends('layouts.landing')

@section('title', 'FAQ — Pertanyaan Umum')

@section('content')

{{-- Header --}}
<div class="bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white pt-[110px] pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/15 text-white text-xs font-bold px-4 py-2 rounded-full mb-6 border border-white/20">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
            Pusat Bantuan
        </div>
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-4">Pertanyaan yang Sering Diajukan</h1>
        <p class="text-primary-200 text-lg max-w-2xl mx-auto">Temukan jawaban atas pertanyaan umum seputar layanan Kecamatan Landasan Ulin.</p>

        {{-- Category tabs --}}
        @if($faqGrouped->isNotEmpty())
        <div class="flex flex-wrap justify-center gap-3 mt-8" id="faq-tabs">
            <button onclick="filterFaq('semua')" data-cat="semua"
                class="faq-tab-btn px-5 py-2 rounded-full text-sm font-semibold transition-all bg-white text-primary-700 shadow">
                Semua
            </button>
            @foreach($faqGrouped->keys() as $kat)
            <button onclick="filterFaq('{{ $kat }}')" data-cat="{{ $kat }}"
                class="faq-tab-btn px-5 py-2 rounded-full text-sm font-semibold transition-all bg-white/20 text-white hover:bg-white hover:text-primary-700">
                {{ $kategoriLabels[$kat] ?? ucfirst($kat) }}
            </button>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- FAQ Content --}}
<div class="bg-gradient-to-b from-blue-50 to-white py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($faqGrouped->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>Belum ada FAQ tersedia.</p>
        </div>
        @else

        @foreach($faqGrouped as $kategori => $faqs)
        <div class="faq-section mb-10" data-cat="{{ $kategori }}">
            {{-- Category badge --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="h-px flex-1 bg-gray-200"></div>
                <span class="px-4 py-1.5 bg-primary-100 text-primary-700 text-xs font-bold rounded-full uppercase tracking-wider">
                    {{ $kategoriLabels[$kategori] ?? ucfirst($kategori) }}
                </span>
                <div class="h-px flex-1 bg-gray-200"></div>
            </div>

            {{-- Accordion items --}}
            <div class="space-y-3" x-data="{ open: null }">
                @foreach($faqs as $i => $faq)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
                    <button
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left"
                        :class="open === {{ $i }} ? 'text-primary-700' : 'text-gray-800'">
                        <span class="font-semibold text-sm pr-4">{{ $faq->pertanyaan }}</span>
                        <svg class="w-5 h-5 shrink-0 transition-transform duration-200 text-primary-500"
                            :class="open === {{ $i }} ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === {{ $i }}"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="px-6 pb-5">
                        <div class="h-px bg-gray-100 mb-4"></div>
                        <p class="text-gray-600 text-sm leading-relaxed">{!! nl2br(e($faq->jawaban)) !!}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        @endif

        {{-- CTA --}}
        <div class="mt-14 bg-gradient-to-br from-primary-600 to-primary-800 rounded-3xl p-8 text-center text-white">
            <h3 class="text-xl font-bold mb-2">Tidak menemukan jawaban?</h3>
            <p class="text-primary-200 text-sm mb-6">Hubungi kami langsung atau kunjungi kantor kecamatan pada jam operasional.</p>
            <a href="{{ route('portal.home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary-700 font-bold rounded-xl hover:bg-primary-50 transition-all text-sm shadow-lg">
                ← Kembali ke Beranda
            </a>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function filterFaq(cat) {
        // Update tab active state
        document.querySelectorAll('.faq-tab-btn').forEach(btn => {
            if (btn.dataset.cat === cat) {
                btn.classList.add('bg-white', 'text-primary-700', 'shadow');
                btn.classList.remove('bg-white/20', 'text-white');
            } else {
                btn.classList.remove('bg-white', 'text-primary-700', 'shadow');
                btn.classList.add('bg-white/20', 'text-white');
            }
        });
        // Show/hide sections
        document.querySelectorAll('.faq-section').forEach(section => {
            if (cat === 'semua' || section.dataset.cat === cat) {
                section.style.display = '';
            } else {
                section.style.display = 'none';
            }
        });
    }
</script>
@endpush