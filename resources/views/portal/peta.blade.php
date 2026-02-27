@extends('layouts.landing')

@section('title', 'Peta Wilayah Kecamatan')

@push('scripts')
{{-- Leaflet CSS di head via stack --}}
@endpush

@section('content')

<div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white pt-[110px] pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-primary-200 text-sm mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">Portal</a>
            <span>/</span>
            <span class="text-white font-medium">Peta Wilayah</span>
        </nav>
        <h1 class="text-3xl font-extrabold">Peta Wilayah Kecamatan</h1>
        <p class="text-primary-100 mt-2">Sebaran fasilitas dan lokasi penting di Kecamatan Landasan Ulin</p>
    </div>
</div>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="petaApp">

    {{-- Filter Kategori --}}
    <div class="flex flex-wrap gap-2 mb-6 min-h-[40px]">
        <template x-if="loading">
            <div class="flex gap-2">
                <template x-for="i in [1,2,3,4,5]">
                    <div class="h-9 w-28 bg-gray-200 animate-pulse rounded-full"></div>
                </template>
            </div>
        </template>

        <template x-if="!loading">
            <div class="flex flex-wrap gap-2 w-full">
                <button @click="toggleKategori('all')"
                    :class="activeKategori === 'all' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-full text-sm font-medium border border-gray-200 transition-colors">
                    📍 Semua Lokasi
                    <span class="ml-1 opacity-70" x-text="'(' + totalMarkers + ')'"></span>
                </button>
                <template x-for="(info, key) in kategoriList" :key="key">
                    <button @click="toggleKategori(key)"
                        :class="activeKategori === key ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-full text-sm font-medium border border-gray-200 transition-colors">
                        <span x-text="info.ikon + ' ' + info.label"></span>
                        <span class="ml-1 opacity-70" x-text="'(' + info.data.length + ')'"></span>
                    </button>
                </template>
            </div>
        </template>
    </div>

    {{-- Layout Peta + Sidebar --}}
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Peta --}}
        <div class="flex-1 relative">
            {{-- Loading overlay --}}
            <div x-show="loading" class="absolute inset-0 z-10 bg-gray-100 rounded-2xl flex flex-col items-center justify-center gap-3" style="height: 560px;">
                <div class="w-10 h-10 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                <p class="text-sm text-gray-500">Memuat data peta...</p>
            </div>

            {{-- Pesan jika tidak ada data --}}
            <div x-show="!loading && totalMarkers === 0" class="absolute inset-0 z-10 bg-gray-50 rounded-2xl flex flex-col items-center justify-center gap-3" style="height:560px;">
                <div class="text-5xl">🗺️</div>
                <p class="text-gray-500 font-medium">Belum ada data lokasi yang tersedia</p>
            </div>

            <div id="map" class="w-full rounded-2xl shadow-lg border border-gray-100" style="height: 560px;"></div>
        </div>

        {{-- Sidebar Info --}}
        <div class="w-full lg:w-80 shrink-0 space-y-4">

            {{-- Statistik --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Statistik Lokasi
                </h3>

                <div x-show="loading" class="space-y-2">
                    <template x-for="i in [1,2,3,4]">
                        <div class="h-8 bg-gray-100 animate-pulse rounded-lg"></div>
                    </template>
                </div>

                <div x-show="!loading">
                    <template x-if="Object.keys(kategoriList).length === 0">
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada data terpetakan</p>
                    </template>
                    <template x-if="Object.keys(kategoriList).length > 0">
                        <div class="space-y-1">
                            <template x-for="(info, key) in kategoriList" :key="'stat-' + key">
                                <button @click="toggleKategori(key)"
                                    :class="activeKategori === key ? 'bg-primary-50 text-primary-700' : 'hover:bg-gray-50 text-gray-600'"
                                    class="w-full flex items-center justify-between py-2 px-3 rounded-lg transition-colors text-left">
                                    <span class="text-sm" x-text="info.ikon + ' ' + info.label"></span>
                                    <span class="text-xs font-semibold bg-white border border-gray-200 px-2 py-0.5 rounded-full" x-text="info.data.length"></span>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Detail Marker terpilih --}}
            <div x-show="selected !== null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white rounded-2xl border border-primary-200 shadow-sm overflow-hidden">

                <div class="w-full h-36 bg-primary-50 overflow-hidden relative">
                    <img x-show="selected && selected.foto" :src="selected ? selected.foto : ''" class="w-full h-full object-cover">
                    <div x-show="selected && !selected.foto" class="w-full h-full flex items-center justify-center text-5xl" x-text="activeIkon"></div>
                    <button @click="selected = null" class="absolute top-2 right-2 w-6 h-6 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center text-xs transition-colors">✕</button>
                </div>

                <div class="p-4">
                    <p class="text-xs text-primary-600 font-medium uppercase tracking-wide mb-1" x-text="activeKategoriLabel"></p>
                    <h4 class="font-bold text-gray-900 text-base mb-1" x-text="selected ? selected.nama : ''"></h4>
                    <p x-show="selected && selected.kelurahan" class="text-xs text-gray-400 mb-2" x-text="selected && selected.kelurahan ? 'Kel. ' + selected.kelurahan : ''"></p>
                    <p x-show="selected && selected.alamat" class="text-sm text-gray-600 mb-1" x-text="selected ? selected.alamat : ''"></p>
                    <p x-show="selected && selected.keterangan" class="text-sm text-gray-500 italic" x-text="selected ? selected.keterangan : ''"></p>
                    <div class="mt-3 pt-3 flex items-center justify-between border-t border-gray-100">
                        <div class="text-xs text-gray-300" x-text="selected ? selected.lat + ', ' + selected.lng : ''"></div>
                        <a x-show="selected" :href="'https://www.google.com/maps/dir/?api=1&destination=' + selected.lat + ',' + selected.lng" target="_blank" class="text-xs font-semibold px-3 py-1.5 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-colors flex items-center gap-1">
                            <span>Petunjuk Arah</span>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Leaflet JS + Alpine component -- diletakkan SEBELUM alpine diinisialisasi --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Daftarkan Alpine component SEBELUM Alpine mulai
    document.addEventListener('alpine:init', () => {
        Alpine.data('petaApp', () => ({
            map: null,
            leafletLoaded: false,
            allData: {},
            kategoriList: {},
            activeKategori: 'all',
            markerGroups: {},
            selected: null,
            activeIkon: '📍',
            activeKategoriLabel: '',
            loading: true,
            totalMarkers: 0,

            init() {
                this.$nextTick(() => {
                    this.initMap();
                    this.loadData();
                });
            },

            initMap() {
                this.map = L.map('map').setView([-3.4556, 114.8424], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19,
                }).addTo(this.map);
            },

            async loadData() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route("peta.data") }}');
                    const json = await res.json();

                    if (json.success) {
                        this.allData = json.data;
                        this.kategoriList = json.data;

                        // Hitung total marker dari semua kategori
                        this.totalMarkers = Object.values(json.data)
                            .reduce((sum, info) => sum + (info.data ? info.data.length : 0), 0);

                        this.renderMarkers('all');
                    }
                } catch (err) {
                    console.error('Gagal memuat data peta:', err);
                } finally {
                    this.loading = false;
                }
            },

            renderMarkers(kategori) {
                // Hapus semua marker layer lama
                Object.values(this.markerGroups).forEach(g => g.clearLayers());
                this.markerGroups = {};

                const toRender = kategori === 'all' ?
                    Object.entries(this.allData) : [
                        [kategori, this.allData[kategori]]
                    ].filter(([, v]) => v);

                const allLatLngs = [];

                toRender.forEach(([key, info]) => {
                    if (!info || !info.data) return;

                    const group = L.layerGroup().addTo(this.map);
                    this.markerGroups[key] = group;

                    info.data.forEach(item => {
                        if (!item.lat || !item.lng) return;

                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lng);
                        const latLng = [lat, lng];
                        allLatLngs.push(latLng);

                        const icon = L.divIcon({
                            html: `<div style="font-size:26px;line-height:1;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.35))">${info.ikon}</div>`,
                            className: '',
                            iconSize: [32, 32],
                            iconAnchor: [16, 32],
                            popupAnchor: [0, -34],
                        });

                        const popup = L.popup({
                            closeButton: false,
                            maxWidth: 220
                        }).setContent(
                            `<div style="font-family:system-ui,sans-serif">
                            <div style="font-weight:700;font-size:14px;margin-bottom:2px">${item.nama}</div>
                            <div style="font-size:11px;color:#6366f1;margin-bottom:4px">${info.label}</div>
                            ${item.alamat ? `<div style="font-size:12px;color:#4b5563;margin-bottom:6px">${item.alamat}</div>` : ''}
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${item.lat},${item.lng}" target="_blank" style="display:inline-block;padding:4px 8px;background:#eef2ff;color:#4f46e5;border-radius:4px;text-decoration:none;font-size:11px;font-weight:600;margin-top:2px;">Google Maps ↗</a>
                         </div>`
                        );

                        const marker = L.marker(latLng, {
                                icon
                            })
                            .addTo(group)
                            .bindPopup(popup);

                        marker.on('click', () => {
                            this.selected = item;
                            this.activeIkon = info.ikon;
                            this.activeKategoriLabel = info.label;
                        });
                    });
                });

                // Auto fitBounds ke semua marker
                if (allLatLngs.length === 1) {
                    this.map.setView(allLatLngs[0], 17);
                } else if (allLatLngs.length > 1) {
                    this.map.fitBounds(allLatLngs, {
                        padding: [40, 40]
                    });
                }
            },

            toggleKategori(key) {
                this.activeKategori = key;
                this.selected = null;
                this.renderMarkers(key);
            },
        }));
    });
</script>

@endsection