@extends('layouts.app')

@section('title', 'Edit Kelurahan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.master.kelurahan.index') }}" class="hover:text-blue-600">Master Data</a>
        <span>/</span>
        <a href="{{ route('admin.master.kelurahan.index') }}" class="hover:text-blue-600">Kelurahan</a>
        <span>/</span>
        <span class="text-gray-800">Edit</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">Edit Kelurahan</h1>
</div>

<form action="{{ route('admin.master.kelurahan.update', $kelurahan->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Kolom Kiri: Data Wilayah --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-800 border-b pb-3">Data Wilayah</h2>

            <div>
                <label for="kecamatan_id" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan <span class="text-red-500">*</span></label>
                <select name="kecamatan_id" id="kecamatan_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $kelurahan->kecamatan_id) == $kecamatan->id ? 'selected' : '' }}>
                        {{ $kecamatan->nama }} ({{ $kecamatan->kabupaten->nama }})
                    </option>
                    @endforeach
                </select>
                @error('kecamatan_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Kelurahan <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('nama', $kelurahan->nama) }}" required>
                @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Kelurahan <span class="text-red-500">*</span></label>
                <input type="text" name="kode" id="kode" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('kode', $kelurahan->kode) }}" required>
                @error('kode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-500">Kode unik Kemendagri.</p>
            </div>

            <div>
                <label for="akronim" class="block text-sm font-medium text-gray-700 mb-1">Akronim Kelurahan</label>
                <input type="text" name="akronim" id="akronim" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('akronim', $kelurahan->akronim) }}" placeholder="Contoh: LU, GM, GPA">
                @error('akronim')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-500">Digunakan untuk penomoran surat, contoh: 470/10/LU/2026.</p>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Kantor</label>
                <input type="text" name="alamat" id="alamat" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('alamat', $kelurahan->alamat) }}" placeholder="Jl. Contoh No. 1, Banjarbaru">
                @error('alamat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="telp" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                <input type="text" name="telp" id="telp" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('telp', $kelurahan->telp) }}" placeholder="0511-xxxxxxx">
                @error('telp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Kolom Kanan: Data Lurah + Kop Surat --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="text-base font-semibold text-gray-800 border-b pb-3">Data Lurah</h2>

                <div>
                    <label for="lurah_nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lurah</label>
                    <input type="text" name="lurah_nama" id="lurah_nama" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('lurah_nama', $kelurahan->lurah_nama) }}" placeholder="Contoh: FAISAL RAHMAN, S.STP, MA">
                    @error('lurah_nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="lurah_nip" class="block text-sm font-medium text-gray-700 mb-1">NIP Lurah</label>
                    <input type="text" name="lurah_nip" id="lurah_nip" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('lurah_nip', $kelurahan->lurah_nip) }}" placeholder="19xxxxxxxxxx">
                    @error('lurah_nip')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="lurah_pangkat" class="block text-sm font-medium text-gray-700 mb-1">Pangkat</label>
                    <input type="text" name="lurah_pangkat" id="lurah_pangkat" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('lurah_pangkat', $kelurahan->lurah_pangkat) }}" placeholder="Contoh: Penata Tk. I">
                    @error('lurah_pangkat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="lurah_golongan" class="block text-sm font-medium text-gray-700 mb-1">Golongan</label>
                    <input type="text" name="lurah_golongan" id="lurah_golongan" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" value="{{ old('lurah_golongan', $kelurahan->lurah_golongan) }}" placeholder="Contoh: III/d">
                    @error('lurah_golongan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800 border-b pb-3">Kop Surat</h2>
                <p class="text-xs text-gray-500">Upload gambar kop surat lengkap (logo + teks instansi). Gambar ini akan langsung digunakan sebagai header di PDF surat.</p>

                @if($kelurahan->kop_surat_path)
                <div class="border border-gray-200 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-2">Kop Surat Saat Ini:</p>
                    <img src="{{ Storage::url($kelurahan->kop_surat_path) }}" alt="Kop Surat" class="w-full rounded border border-gray-100">
                </div>
                @endif

                <div>
                    <label for="kop_surat" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $kelurahan->kop_surat_path ? 'Ganti Kop Surat' : 'Upload Kop Surat' }}
                    </label>
                    <input type="file" name="kop_surat" id="kop_surat" accept="image/jpg,image/jpeg,image/png"
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-400">JPG/PNG, maks. 2MB. Resolusi disarankan minimal 1000px lebar.</p>
                    @error('kop_surat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('admin.master.kelurahan.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan Perubahan</button>
    </div>
</form>
@endsection