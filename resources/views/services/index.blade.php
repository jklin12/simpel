@extends('layouts.landing')

@section('title', 'Layanan Surat Menyurat')

@section('content')

@if(session('success_application'))
<!-- Success Modal / Alert -->
@push('scripts')
<script>
    if (typeof gtag !== 'undefined') {
        gtag('event', 'permohonan_submitted', {
            token: '{{ session('success_application.token') }}'
        });
    }
</script>
@endpush
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto" x-data="{ show: true }" x-show="show" @click.self="show = false">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 my-auto">
        <div class="flex justify-center mb-4">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        <h3 class="text-center text-lg font-medium text-gray-900 mb-2">Permohonan Berhasil Diajukan!</h3>
        <p class="text-center text-sm text-gray-500 mb-6">{{ session('success_application.message') }}</p>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-xs text-gray-600 mb-1">Kode Tracking Anda:</p>
            <p class="text-lg font-bold text-blue-600 text-center break-all">{{ session('success_application.token') }}</p>
        </div>
        <div class="space-y-3">
            <a href="{{ route('layanan.surat.tracking') }}" @click="show = false" class="w-full inline-flex justify-center items-center h-12 rounded-xl bg-primary-600 text-white px-4 font-semibold hover:bg-primary-700 transition-colors">
                Cek Status Permohonan
            </a>
            <button @click="show = false" class="w-full inline-flex justify-center items-center h-12 rounded-xl border border-gray-300 bg-white text-gray-700 px-4 font-medium hover:bg-gray-50 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
@endif

@php
    // Kelompokkan jenis surat per kategori (config/layanan.php); sisanya masuk "Lainnya".
    $kategoriConfig = config('layanan.kategori');
    $kodeKeKategori = [];
    foreach ($kategoriConfig as $key => $kat) {
        foreach ($kat['kode'] as $kode) {
            $kodeKeKategori[$kode] = $key;
        }
    }

    $groups = [];
    foreach ($kategoriConfig as $key => $kat) {
        $groups[$key] = ['key' => $key, 'label' => $kat['label'], 'tone' => $kat['tone'], 'items' => []];
    }
    $groups['lainnya'] = ['key' => 'lainnya'] + config('layanan.kategori_lainnya') + ['items' => []];

    foreach ($services as $service) {
        $key = $kodeKeKategori[$service->kode] ?? 'lainnya';
        $groups[$key]['items'][] = [
            'id' => $service->id,
            'kode' => $service->kode,
            'nama' => $service->nama,
            'text' => Str::lower($service->kode . ' ' . $service->nama . ' ' . $service->deskripsi),
        ];
    }
    $groups = collect($groups)->filter(fn ($g) => count($g['items']) > 0)->values();

    $kelurahanList = $kelurahans->map(fn ($k) => ['id' => $k->id, 'nama' => $k->nama])->values();
@endphp

<div x-data="{
    q: '',
    cat: 'all',
    groups: {{ Js::from($groups) }},
    kelurahans: {{ Js::from($kelurahanList) }},
    sheet: false,
    svc: null,
    kel: '',
    kq: '',
    agreed: false,

    get visibleGroups() {
        const k = this.q.toLowerCase().trim();
        return this.groups
            .filter(g => this.cat === 'all' || g.key === this.cat)
            .map(g => ({ ...g, items: g.items.filter(i => k === '' || i.text.includes(k)) }))
            .filter(g => g.items.length > 0);
    },
    get visibleKelurahans() {
        const k = this.kq.toLowerCase().trim();
        return this.kelurahans.filter(x => k === '' || x.nama.toLowerCase().includes(k));
    },
    openSheet(item) {
        this.svc = item;
        this.kel = '';
        this.kq = '';
        this.agreed = false;
        this.sheet = true;
    },
    go() {
        if (!this.agreed || !this.kel || !this.svc) return;
        window.location.href = '{{ route('layanan.surat.ajukan') }}?service_id=' + this.svc.id + '&kelurahan_id=' + this.kel;
    }
}" x-effect="document.body.style.overflow = sheet ? 'hidden' : ''" @keydown.escape.window="sheet = false">

    <header class="max-w-5xl mx-auto px-4 pt-5 pb-3 lg:pt-10">
        <h1 class="text-2xl lg:text-4xl font-bold leading-tight text-gray-900">Daftar Layanan Surat</h1>
        <p class="text-sm lg:text-base text-gray-500 mt-1">Pilih jenis surat, lalu ajukan permohonan dalam beberapa langkah.</p>
    </header>

    {{-- Pencarian + filter kategori (menempel di bawah navbar) --}}
    <div class="sticky top-14 lg:top-16 z-30 bg-gray-50/95 backdrop-blur px-4 pt-1 pb-3 border-b border-gray-200">
        <div class="max-w-5xl mx-auto">
            <label class="flex items-center gap-2 h-12 rounded-xl bg-white border border-gray-300 px-3.5 text-gray-400 focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500">
                <svg class="w-5 h-5 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" /></svg>
                <input type="search" x-model.debounce.150ms="q" placeholder='Cari surat, mis. "tidak mampu"'
                    class="flex-1 min-w-0 bg-transparent border-0 p-0 text-[15px] text-gray-900 placeholder-gray-400 focus:ring-0 focus:outline-none">
            </label>
            <div class="flex gap-2 overflow-x-auto mt-2.5 -mx-4 px-4 pb-0.5" style="scrollbar-width: none;">
                <button type="button" @click="cat = 'all'"
                    :class="cat === 'all' ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700'"
                    class="flex-none h-9 px-4 rounded-full text-[13px] font-medium whitespace-nowrap">Semua</button>
                <template x-for="g in groups" :key="g.key">
                    <button type="button" @click="cat = g.key" x-text="g.label"
                        :class="cat === g.key ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700'"
                        class="flex-none h-9 px-4 rounded-full text-[13px] font-medium whitespace-nowrap"></button>
                </template>
            </div>
        </div>
    </div>

    {{-- Daftar per kategori --}}
    <section id="buat-surat" class="max-w-5xl mx-auto px-4 mt-4 space-y-6">
        <template x-for="g in visibleGroups" :key="g.key">
            <div>
                <p class="text-[11px] font-semibold tracking-wider uppercase text-gray-400 mb-2" x-text="g.label + ' · ' + g.items.length"></p>
                <div class="space-y-2 lg:grid lg:grid-cols-2 lg:gap-3 lg:space-y-0">
                    <template x-for="item in g.items" :key="item.id">
                        <button type="button" @click="openSheet(item)"
                            class="w-full flex items-center gap-3 bg-white rounded-xl border border-gray-100 p-3 min-h-[64px] text-left hover:border-primary-200 transition-colors">
                            <span class="w-12 h-12 rounded-lg flex-none flex items-center justify-center text-[10px] font-bold" :class="g.tone" x-text="item.kode"></span>
                            <span class="flex-1 min-w-0 text-[14px] font-semibold leading-snug text-gray-900 line-clamp-2" x-text="item.nama"></span>
                            <svg class="w-5 h-5 text-gray-300 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <p x-show="visibleGroups.length === 0" x-cloak class="text-center text-sm text-gray-500 py-10">
            @if($services->isEmpty())
            Belum ada layanan tersedia.
            @else
            Layanan tidak ditemukan. Coba kata kunci lain.
            @endif
        </p>
    </section>

    {{-- Alur pengajuan (pengganti infografis gambar) --}}
    <section class="max-w-5xl mx-auto mt-8">
        <h2 class="px-4 text-lg font-bold text-gray-900">Alur pengajuan</h2>
        <div class="flex gap-3 overflow-x-auto snap-x snap-mandatory px-4 mt-3 pb-1 lg:grid lg:grid-cols-4 lg:overflow-visible" style="scrollbar-width: none;">
            @foreach([['Pilih layanan', 'Cari dan pilih jenis surat.'], ['Isi data', 'Isi formulir dan unggah berkas.'], ['Simpan kode', 'Catat kode tracking dari WhatsApp.'], ['Unduh surat', 'Pantau status, lalu unduh surat.']] as $i => [$judul, $isi])
            <div class="snap-start flex-none w-40 lg:w-auto bg-white rounded-2xl border border-gray-100 p-4">
                <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                <p class="text-[15px] font-semibold mt-2.5 text-gray-900">{{ $judul }}</p>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $isi }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Banner WhatsApp (pengganti popup layar penuh); konten tetap dikelola admin --}}
    @if($popup->is_active && $popup->gambar)
    <section x-data="{ show: true }" x-show="show" class="max-w-5xl mx-auto px-4 mt-6">
        <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-4 relative">
            <button type="button" @click="show = false" aria-label="Tutup" class="absolute top-1 right-1 w-11 h-11 flex items-center justify-center text-emerald-700/60 hover:text-emerald-800">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12" /></svg>
            </button>
            <div class="flex items-start gap-3 pr-8">
                <span class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.7A8.4 8.4 0 0 1 3.5 11.5 8.5 8.5 0 0 1 12 3a8.4 8.4 0 0 1 9 8.5z" /></svg>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-[15px] font-semibold text-emerald-900">Kirim pesan WhatsApp dulu</p>
                    <p class="text-xs text-emerald-800 mt-0.5 leading-relaxed">Agar notifikasi proses permohonan sampai, kirim satu pesan ke nomor kami sebelum mengajukan. Tidak perlu menunggu balasan.</p>
                    <a href="https://wa.me/{{ $popup->normalized_wa_number }}?text={{ rawurlencode($popup->wa_message ?? '') }}" target="_blank" rel="noopener"
                        class="inline-flex items-center justify-center h-11 px-4 mt-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">
                        {{ $popup->button_text ?: 'Mulai Chat' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="max-w-5xl mx-auto px-4 mt-3">
        <div class="rounded-2xl bg-primary-600 text-white p-4 flex items-center gap-3">
            <div class="flex-1">
                <p class="text-[15px] font-semibold">Butuh bantuan lain?</p>
                <p class="text-xs text-primary-100">Lihat jawaban di halaman FAQ.</p>
            </div>
            <a href="{{ route('faq') }}" class="h-11 px-4 rounded-lg bg-white text-primary-700 text-sm font-semibold flex items-center">Buka FAQ</a>
        </div>
    </section>

    {{-- Bottom sheet pilih kelurahan (modal di layar lebar) --}}
    <div x-show="sheet" x-cloak class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/50" @click="sheet = false" x-show="sheet"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <div x-show="sheet" class="relative w-full sm:max-w-md bg-white rounded-t-3xl sm:rounded-2xl pt-3 pb-5 px-4 shadow-2xl max-h-[90vh] flex flex-col"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0" x-transition:enter-end="translate-y-0 sm:opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full sm:translate-y-4 sm:opacity-0">
            <div class="w-10 h-1 rounded-full bg-gray-300 mx-auto sm:hidden"></div>
            <div class="flex items-start justify-between mt-3">
                <div class="min-w-0">
                    <p class="text-lg font-bold text-gray-900">Pilih kelurahan</p>
                    <p class="text-xs text-gray-500">Untuk <span class="font-semibold text-gray-700" x-text="svc?.nama"></span></p>
                </div>
                <button type="button" @click="sheet = false" aria-label="Tutup" class="w-11 h-11 -mr-2 -mt-1 flex-none flex items-center justify-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12" /></svg>
                </button>
            </div>

            <label class="flex items-center gap-2 h-11 rounded-xl bg-gray-50 border border-gray-300 px-3.5 mt-2 text-gray-400">
                <svg class="w-5 h-5 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z" /></svg>
                <input type="search" x-model="kq" placeholder="Cari kelurahan" class="flex-1 min-w-0 bg-transparent border-0 p-0 text-sm text-gray-900 placeholder-gray-400 focus:ring-0 focus:outline-none">
            </label>

            <div class="mt-2 divide-y divide-gray-100 overflow-y-auto flex-1 min-h-0" role="radiogroup">
                <template x-for="k in visibleKelurahans" :key="k.id">
                    <button type="button" role="radio" :aria-checked="kel == k.id" @click="kel = k.id" class="w-full flex items-center gap-3 h-12 text-left">
                        <span class="w-5 h-5 rounded-full border-2 flex-none flex items-center justify-center" :class="kel == k.id ? 'border-primary-600' : 'border-gray-300'">
                            <span x-show="kel == k.id" class="w-2.5 h-2.5 rounded-full bg-primary-600"></span>
                        </span>
                        <span class="text-[15px]" :class="kel == k.id ? 'font-semibold text-gray-900' : 'text-gray-700'" x-text="k.nama"></span>
                    </button>
                </template>
                <p x-show="visibleKelurahans.length === 0" class="text-sm text-gray-500 py-4 text-center">Kelurahan tidak ditemukan.</p>
            </div>
            <p class="text-[11px] text-gray-500 mt-2">Layanan ini khusus untuk wilayah Kecamatan Landasan Ulin.</p>

            <label class="flex items-start gap-3 mt-3 cursor-pointer">
                <input type="checkbox" x-model="agreed" class="mt-0.5 h-5 w-5 flex-none text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="text-[13px] text-gray-600 leading-snug">Saya menyetujui syarat dan ketentuan pengajuan surat ini dan menjamin kebenaran data yang diberikan.</span>
            </label>

            <button type="button" @click="go()" :disabled="!agreed || !kel"
                class="mt-4 flex items-center justify-center gap-2 h-12 w-full rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                Lanjut mengisi data
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>
</div>

@include('components.success-modal')
@endsection
