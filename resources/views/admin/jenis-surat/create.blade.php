@extends('layouts.app')

@section('title', 'Tambah Jenis Surat')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.jenis-surat.index') }}" class="hover:text-blue-600">Jenis Surat</a>
        <span>/</span>
        <span class="text-gray-800">Tambah Baru</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">Tambah Jenis Surat Baru</h1>
</div>

{{-- Flash Messages --}}
@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start gap-3" role="alert">
    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
    </svg>
    <div>
        <p class="font-semibold">Error!</p>
        <p class="text-sm">{{ session('error') }}</p>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
    <p class="font-semibold mb-2">Terdapat kesalahan pada form:</p>
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-4xl">
    <form action="{{ route('admin.jenis-surat.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Jenis Surat</label>
                <input type="text" name="nama" id="nama" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: Surat Keterangan Domisili" value="{{ old('nama') }}" required>
                @error('nama')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Surat</label>
                <input type="text" name="kode" id="kode" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: SKD" value="{{ old('kode') }}" required>
                @error('kode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Kode unik untuk identifikasi jenis surat.</p>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Deskripsi singkat tentang jenis surat ini...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" value="1" name="is_active" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Aktifkan jenis surat ini
                </label>
            </div>

            {{-- Attachment Guides Builder --}}
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Petunjuk Lampiran</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Petunjuk per file attachment yang akan ditampilkan kepada user di bawah setiap input file.</p>
                    </div>
                    <button type="button" id="add-guide-btn" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium transition shadow-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Petunjuk
                    </button>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm" id="guides-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-48">Nama Field</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contoh</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="guides-body"></tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-400 mt-2">Nama field harus sesuai field attachment, contoh: <code>surat_pengantar_rtrw</code></p>
            </div>

            {{-- OCR Rules Configuration --}}
            <div class="pt-4 border-t border-gray-100">
                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Aturan Verifikasi OCR</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Konfigurasi AI untuk verifikasi otomatis dokumen pendukung. Format JSON. <a href="#" onclick="event.preventDefault(); document.getElementById('ocr-example').classList.toggle('hidden')" class="text-blue-600 underline">Lihat contoh</a></p>
                </div>
                <pre id="ocr-example" class="hidden text-xs bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3 overflow-x-auto"><code>{
    "instruksi_global": "Cross-check semua data pribadi di seluruh dokumen.",
    "dokumen": [
        {
            "jenis_dokumen": "ktp_kk_bersangkutan",
            "label": "KTP & KK yang Bersangkutan",
            "wajib": true,
            "instruksi": "Bandingkan data pemohon dengan data di KTP dan KK."
        },
        {
            "jenis_dokumen": "surat_pengantar_rtrw",
            "label": "Surat Pengantar RT/RW",
            "wajib": true,
            "instruksi": "Periksa apakah nama dan alamat pemohon di surat pengantar sesuai."
        }
    ]
}</code></pre>
                <textarea name="ocr_rules" id="ocr_rules" rows="8" class="w-full rounded-lg border-gray-300 font-mono text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder='Isi dengan JSON konfigurasi OCR atau kosongkan jika tidak menggunakan verifikasi AI.'>{{ old('ocr_rules') }}</textarea>
                @error('ocr_rules')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-2">
                    <code>jenis_dokumen</code> harus sesuai dengan nama field jenis dokumen di <code>PermohonanDokumen::JENIS_DOKUMEN</code>.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.jenis-surat.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan</button>
            </div>
        </div>
    </form>
</div>

<template id="guide-row-template">
    <tr class="border-t border-gray-100 guide-row">
        <td class="px-3 py-2"><input type="text" class="guide-field-input w-full rounded border-gray-300 text-sm shadow-sm" placeholder="surat_pengantar_rtrw"></td>
        <td class="px-3 py-2"><input type="text" class="guide-keterangan-input w-full rounded border-gray-300 text-sm shadow-sm" placeholder="Keterangan singkat"></td>
        <td class="px-3 py-2"><input type="text" class="guide-contoh-input w-full rounded border-gray-300 text-sm shadow-sm" placeholder="Format, ukuran, dsb."></td>
        <td class="px-3 py-2 text-center"><button type="button" class="remove-guide-btn text-red-500 hover:text-red-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg></button></td>
    </tr>
</template>

<script>
    (function() {
        // ── Attachment Guides ──────────────────────────────────────────
        const guidesTbody = document.getElementById('guides-body');

        function addGuideRow(fieldName, keterangan, contoh) {
            const html = `<tr class="border-t border-gray-100 guide-row">
                <td class="px-3 py-2"><input type="text" class="guide-field-input w-full rounded border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="surat_pengantar_rtrw"></td>
                <td class="px-3 py-2"><input type="text" class="guide-keterangan-input w-full rounded border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: Surat pengantar asli dari RT/RW"></td>
                <td class="px-3 py-2"><input type="text" class="guide-contoh-input w-full rounded border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Contoh: Foto berwarna, format PDF max 5MB"></td>
                <td class="px-3 py-2 text-center"><button type="button" class="remove-guide-btn text-red-500 hover:text-red-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>
            </tr>`;
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = html;
            const row = wrapper.firstElementChild;
            row.querySelector('.guide-field-input').value = fieldName || '';
            row.querySelector('.guide-keterangan-input').value = keterangan || '';
            row.querySelector('.guide-contoh-input').value = contoh || '';
            row.querySelector('.remove-guide-btn').addEventListener('click', () => row.remove());
            guidesTbody.appendChild(row);
        }

        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('input[name^="attachment_guides["]').forEach(el => el.remove());
            guidesTbody.querySelectorAll('.guide-row').forEach(function(row) {
                const fieldName = row.querySelector('.guide-field-input').value.trim();
                if (!fieldName) return;
                const keterangan = row.querySelector('.guide-keterangan-input').value.trim();
                const contoh = row.querySelector('.guide-contoh-input').value.trim();

                function appendHidden(name, value) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = value;
                    document.querySelector('form').appendChild(inp);
                }
                appendHidden(`attachment_guides[${fieldName}][keterangan]`, keterangan);
                appendHidden(`attachment_guides[${fieldName}][contoh]`, contoh);
            });
        });

        document.getElementById('add-guide-btn').addEventListener('click', () => addGuideRow('', '', ''));

        @if(old('attachment_guides'))
        const existingGuides = @json(old('attachment_guides'));
        Object.entries(existingGuides).forEach(([fn, g]) => addGuideRow(fn, g.keterangan || '', g.contoh || ''));
        @endif
    })();
</script>
@endsection