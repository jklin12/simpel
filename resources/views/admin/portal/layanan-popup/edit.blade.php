@extends('layouts.app')

@section('title', 'Popup Chat WhatsApp')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Popup Chat WhatsApp</h1>
    <p class="text-gray-500 text-sm">Atur gambar popup dan nomor WhatsApp yang muncul di halaman /layanan.</p>
</div>

@if($errors->any())
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
@endif

<form
    action="{{ route('admin.portal.layanan-popup.update') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom kiri: gambar --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3 mb-4">Gambar Popup</h3>
                @if($popup->gambar)
                <div class="mb-3">
                    <p class="text-xs text-gray-500 mb-2">Gambar saat ini:</p>
                    <img src="{{ asset('storage/' . $popup->gambar) }}" class="w-full max-h-80 object-contain rounded-xl border border-gray-100">
                </div>
                @endif
                <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-8 cursor-pointer hover:bg-gray-50 transition-colors">
                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Klik untuk upload gambar</span>
                    <span class="text-xs text-gray-400 mt-1">JPG/PNG, maks 2MB. Popup tidak akan tampil di halaman publik jika gambar belum diisi.</span>
                    <input type="file" name="gambar" accept="image/jpg,image/jpeg,image/png" class="hidden" id="gambar-input" onchange="previewImage(this)">
                </label>
                <img id="gambar-preview" class="mt-3 w-full max-h-80 object-contain rounded-xl hidden border border-gray-100">
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Tombol WhatsApp</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" name="wa_number" value="{{ old('wa_number', $popup->wa_number) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Contoh: 081234567890">
                    <p class="text-xs text-gray-400 mt-1">Boleh diawali 0, 62, atau 8 — otomatis dinormalisasi ke format 62 saat dipakai.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan WhatsApp</label>
                    <textarea name="wa_message" rows="3"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Pesan yang otomatis terisi saat pengunjung klik tombol Mulai Chat">{{ old('wa_message', $popup->wa_message) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teks Tombol</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $popup->button_text) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Mulai Chat">
                </div>
            </div>
        </div>

        {{-- Kolom kanan: status --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Status</h3>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                        @checked(old('is_active', $popup->is_active))
                        class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="text-sm text-gray-700">Tampilkan popup di halaman /layanan</span>
                </label>
                <p class="text-xs text-gray-400">Popup hanya tampil jika status aktif dan gambar sudah diupload.</p>
            </div>

            <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all shadow-sm text-sm">
                💾 Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('gambar-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
