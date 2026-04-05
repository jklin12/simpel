@extends('layouts.app')

@section('title', 'Tambah Kelurahan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.master.kelurahan.index') }}" class="hover:text-blue-600">Master Data</a>
        <span>/</span>
        <a href="{{ route('admin.master.kelurahan.index') }}" class="hover:text-blue-600">Kelurahan</a>
        <span>/</span>
        <span class="text-gray-800">Tambah Baru</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">Tambah Kelurahan Baru</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.master.kelurahan.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label for="kecamatan_id" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                <select name="kecamatan_id" id="kecamatan_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                        {{ $kecamatan->nama }} ({{ $kecamatan->kabupaten->nama }})
                    </option>
                    @endforeach
                </select>
                @error('kecamatan_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Kelurahan</label>
                <input type="text" name="nama" id="nama" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: Guntung Manggis" value="{{ old('nama') }}" required>
                @error('nama')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Kelurahan</label>
                <input type="text" name="kode" id="kode" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: 63.72.01.1001" value="{{ old('kode') }}" required>
                @error('kode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Kode unik untuk identifikasi wilayah (Kemendagri).</p>
            </div>

            <div>
                <label for="akronim" class="block text-sm font-medium text-gray-700 mb-1">Akronim Kelurahan</label>
                <input type="text" name="akronim" id="akronim" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: LU, GM, GPA" value="{{ old('akronim') }}">
                @error('akronim')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Digunakan untuk penomoran surat, contoh: 470/10/LU/2026.</p>
            </div>

            <div>
                <label for="lurah_no_hp" class="block text-sm font-medium text-gray-700 mb-1">No HP Lurah (WhatsApp)</label>
                <input type="text" name="lurah_no_hp" id="lurah_no_hp" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: 081234567890" value="{{ old('lurah_no_hp') }}">
                @error('lurah_no_hp')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Menerima notifikasi bot WA untuk persetujuan surat masuk.</p>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status_pejabat" class="block text-sm font-medium text-gray-700 mb-1">Status Jabatan Pejabat <span class="text-red-500">*</span></label>
                        <select name="status_pejabat" id="status_pejabat" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                            <option value="Definitif" {{ old('status_pejabat') == 'Definitif' ? 'selected' : '' }}>Definitif</option>
                            <option value="Plh" {{ old('status_pejabat') == 'Plh' ? 'selected' : '' }}>PLH</option>
                            <option value="Plt" {{ old('status_pejabat') == 'Plt' ? 'selected' : '' }}>PLT</option>
                        </select>
                        @error('status_pejabat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">Mempengaruhi penulisan jabatan di PDF (contoh: Plh. Lurah ...)</p>
                    </div>

                    <div class="flex items-center">
                        <label class="inline-flex items-center cursor-pointer mt-6">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700">Aktif (Tampil di Layanan Publik)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.master.kelurahan.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection