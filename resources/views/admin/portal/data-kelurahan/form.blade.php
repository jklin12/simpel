@extends('layouts.app')

@section('title', isset($item) ? 'Edit Data' : 'Tambah Data Kelurahan')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.portal.data-kelurahan.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($item) ? 'Edit Data Kelurahan' : 'Tambah Data Kelurahan' }}</h1>
        <p class="text-gray-500 text-sm">Isi detail data dan klik peta untuk menentukan koordinat lokasi</p>
    </div>
</div>

<form action="{{ isset($item) ? route('admin.portal.data-kelurahan.update', $item->id) : route('admin.portal.data-kelurahan.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($item)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Form Fields --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4" x-data="{ kategori: '{{ old('kategori', $item->kategori ?? '') }}' }">
                <h3 class="font-semibold text-gray-800">Informasi Dasar</h3>

                {{-- Kelurahan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kelurahan <span class="text-red-500">*</span></label>
                    @hasanyrole('admin_kecamatan|admin_kabupaten|super_admin')
                    {{-- Admin Kecamatan/Super Admin: bisa pilih kelurahan mana saja --}}
                    <select name="kelurahan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('kelurahan_id') border-red-400 @enderror">
                        <option value="">— Pilih Kelurahan —</option>
                        @foreach($kelurahans as $kel)
                        <option value="{{ $kel->id }}" @selected(old('kelurahan_id', $item->kelurahan_id ?? '') == $kel->id)>{{ $kel->nama }}</option>
                        @endforeach
                    </select>
                    @else
                    {{-- Admin Kelurahan: kelurahan sudah dikunci ke kelurahan mereka --}}
                    <input type="hidden" name="kelurahan_id" value="{{ Auth::user()->kelurahan_id }}">
                    <div class="flex items-center gap-2 px-3 py-2.5 bg-blue-50 border border-blue-200 rounded-lg">
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm font-medium text-blue-700">{{ Auth::user()->kelurahan?->nama ?? 'Kelurahan Anda' }}</span>
                        <span class="text-xs text-blue-500 ml-auto">(dikunci ke kelurahan Anda)</span>
                    </div>
                    @endhasanyrole
                    @error('kelurahan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" x-model="kategori" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('kategori') border-red-400 @enderror">
                        <option value="">— Pilih Kategori —</option>
                        @foreach($kategoriList as $key => $label)
                        <option value="{{ $key }}" @selected(old('kategori', $item->kategori ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Dependent: Jenis Fasilitas (Ibadah, Pemakaman, Pendidikan, Kesehatan, Keamanan) --}}
                <div x-show="['tempat_ibadah', 'pemakaman', 'sarana_pendidikan', 'fasilitas_kesehatan', 'fasilitas_keamanan'].includes(kategori)" x-cloak>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Fasilitas</label>
                    <select name="jenis_fasilitas" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('jenis_fasilitas') border-red-400 @enderror">
                        <option value="">— Pilih Jenis (Opsional) —</option>

                        <template x-if="kategori === 'tempat_ibadah'">
                            <optgroup label="Tipe Tempat Ibadah">
                                @foreach($options['ibadah'] as $opt)
                                <option value="{{ $opt }}" @selected(old('jenis_fasilitas', $item->jenis_fasilitas ?? '') == $opt)>{{ $opt }}</option>
                                @endforeach
                            </optgroup>
                        </template>

                        <template x-if="kategori === 'pemakaman'">
                            <optgroup label="Tipe Tempat Pemakaman">
                                @foreach($options['pemakaman'] as $opt)
                                <option value="{{ $opt }}" @selected(old('jenis_fasilitas', $item->jenis_fasilitas ?? '') == $opt)>{{ $opt }}</option>
                                @endforeach
                            </optgroup>
                        </template>

                        <template x-if="kategori === 'sarana_pendidikan'">
                            <optgroup label="Tipe Sarana Pendidikan">
                                @foreach($options['pendidikan'] as $opt)
                                <option value="{{ $opt }}" @selected(old('jenis_fasilitas', $item->jenis_fasilitas ?? '') == $opt)>{{ $opt }}</option>
                                @endforeach
                            </optgroup>
                        </template>

                        <template x-if="kategori === 'fasilitas_kesehatan'">
                            <optgroup label="Tipe Fasilitas Kesehatan">
                                @foreach($options['kesehatan'] as $opt)
                                <option value="{{ $opt }}" @selected(old('jenis_fasilitas', $item->jenis_fasilitas ?? '') == $opt)>{{ $opt }}</option>
                                @endforeach
                            </optgroup>
                        </template>

                        <template x-if="kategori === 'fasilitas_keamanan'">
                            <optgroup label="Tipe Fasilitas Keamanan">
                                @foreach($options['keamanan'] as $opt)
                                <option value="{{ $opt }}" @selected(old('jenis_fasilitas', $item->jenis_fasilitas ?? '') == $opt)>{{ $opt }}</option>
                                @endforeach
                            </optgroup>
                        </template>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika jenis tidak ada di pilihan.</p>
                </div>

                {{-- Dependent: Status Fasilitas (Hanya Pendidikan & Kesehatan) --}}
                <div x-show="['sarana_pendidikan', 'fasilitas_kesehatan'].includes(kategori)" x-cloak>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status Instansi</label>
                    <select name="status_fasilitas" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('status_fasilitas') border-red-400 @enderror">
                        <option value="">— Pilih Status (Opsional) —</option>
                        @foreach($options['status'] as $opt)
                        <option value="{{ $opt }}" @selected(old('status_fasilitas', $item->status_fasilitas ?? '') == $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- RT dan RW --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">RT</label>
                        <input type="text" name="rt" value="{{ old('rt', $item->rt ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Contoh: 001">
                        @error('rt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">RW</label>
                        <input type="text" name="rw" value="{{ old('rw', $item->rw ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Contoh: 015">
                        @error('rw') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $item->nama ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500 @error('nama') border-red-400 @enderror"
                        placeholder="Nama RW, masjid, sekolah, dll...">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat</label>
                    <input type="text" name="alamat" value="{{ old('alamat', $item->alamat ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Alamat lengkap...">
                    @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Deskripsi singkat tentang lokasi ini...">{{ old('keterangan', $item->keterangan ?? '') }}</textarea>
                </div>
            </div>

            {{-- Koordinat --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Koordinat Lokasi</h3>
                    <span class="text-xs text-primary-600 bg-primary-50 px-2 py-1 rounded-full">Klik peta untuk mengisi otomatis</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Latitude</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $item->latitude ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500 @error('latitude') border-red-400 @enderror"
                            placeholder="-3.4556" step="any" inputmode="decimal">
                        @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Longitude</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $item->longitude ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500 @error('longitude') border-red-400 @enderror"
                            placeholder="114.8424" step="any" inputmode="decimal">
                        @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-400">Kosongkan jika lokasi tidak perlu ditampilkan di peta.</p>
            </div>

            {{-- Foto --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ preview: '{{ isset($item) && $item->foto ? asset('storage/' . $item->foto) : '' }}' }">
                <h3 class="font-semibold text-gray-800 mb-3">Foto</h3>
                <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                    @change="preview = URL.createObjectURL($event.target.files[0])">
                <label for="foto" class="flex flex-col items-center justify-center gap-2 p-6 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-primary-400 transition" x-show="!preview">
                    <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs text-gray-400">Klik untuk upload foto lokasi</span>
                </label>
                <div x-show="preview" class="relative">
                    <img :src="preview" class="w-full h-40 object-cover rounded-xl">
                    <button type="button" @click="preview = ''; document.getElementById('foto').value = ''" class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs">✕</button>
                </div>
                @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.portal.data-kelurahan.index') }}" class="flex-1 text-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="flex-1 px-4 py-3 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 shadow-sm transition">
                    {{ isset($item) ? 'Simpan Perubahan' : 'Tambah Data' }}
                </button>
            </div>
        </div>

        {{-- Map Picker --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" id="map-picker-container">
            <h3 class="font-semibold text-gray-800 mb-2">Pilih Lokasi di Peta</h3>
            <p class="text-xs text-gray-400 mb-4">Klik pada peta untuk menentukan koordinat secara visual. Koordinat akan terisi otomatis.</p>
            <div id="map-picker" class="w-full rounded-xl overflow-hidden border border-gray-100" style="height: 500px;"></div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const initLat = parseFloat(document.getElementById('latitude').value) || -3.4556;
        const initLng = parseFloat(document.getElementById('longitude').value) || 114.8424;
        const hasPoint = !isNaN(parseFloat(document.getElementById('latitude').value));

        const map = L.map('map-picker').setView([initLat, initLng], hasPoint ? 16 : 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19,
        }).addTo(map);

        let marker = null;

        function placeMarker(lat, lng) {
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('latitude').value = pos.lat.toFixed(7);
                document.getElementById('longitude').value = pos.lng.toFixed(7);
            });
        }

        if (hasPoint) placeMarker(initLat, initLng);

        map.on('click', function(e) {
            placeMarker(e.latlng.lat, e.latlng.lng);
        });
    });
</script>
@endpush