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

            {{-- Field Permohonan Builder --}}
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Field Permohonan</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Definisikan field yang akan muncul di form pengajuan publik.</p>
                    </div>
                    <button type="button" id="add-field-btn" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-medium transition shadow-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Field
                    </button>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm" id="fields-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-36">Nama Field</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Label</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Tipe</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-16">Wajib</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Opsi (jika select, pisah koma)</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="fields-body">
                            {{-- Rows populated by JS --}}
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-400 mt-2">Nama field: huruf kecil dan underscore saja, contoh: <code>nama_lengkap</code></p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.jenis-surat.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan</button>
            </div>
        </div>
    </form>
</div>

<template id="field-row-template">
    <tr class="border-t border-gray-100 field-row">
        <td class="px-3 py-2">
            <input type="text" name="required_fields[__INDEX__][name]" placeholder="nama_field"
                pattern="[a-z_]+" title="Huruf kecil dan underscore saja"
                class="w-full rounded border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </td>
        <td class="px-3 py-2">
            <input type="text" name="required_fields[__INDEX__][label]" placeholder="Label tampilan"
                class="w-full rounded border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </td>
        <td class="px-3 py-2">
            <select name="required_fields[__INDEX__][type]" class="w-full rounded border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm field-type-select">
                <option value="text">Text</option>
                <option value="textarea">Textarea</option>
                <option value="date">Date</option>
                <option value="number">Number</option>
                <option value="select">Select</option>
                <option value="file">File</option>
            </select>
        </td>
        <td class="px-3 py-2 text-center">
            <input type="checkbox" name="required_fields[__INDEX__][is_required]" value="1"
                class="rounded border-gray-300 text-blue-600 shadow-sm">
        </td>
        <td class="px-3 py-2">
            <input type="text" name="required_fields[__INDEX__][options]" placeholder="Opsi1,Opsi2,Opsi3"
                class="w-full rounded border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm options-input" disabled style="opacity:0.4">
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="remove-field-btn text-red-500 hover:text-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </td>
    </tr>
</template>

<script>
(function () {
    let fieldIndex = 0;

    function addFieldRow(data) {
        const template = document.getElementById('field-row-template').innerHTML;
        const html = template.replace(/__INDEX__/g, fieldIndex++);
        const tbody = document.getElementById('fields-body');
        const tr = document.createElement('tbody');
        tr.innerHTML = html;
        const row = tr.firstElementChild;

        if (data) {
            row.querySelector('[name$="[name]"]').value = data.name || '';
            row.querySelector('[name$="[label]"]').value = data.label || '';
            row.querySelector('[name$="[type]"]').value = data.type || 'text';
            row.querySelector('[name$="[is_required]"]').checked = !!data.is_required;
            const optionsVal = Array.isArray(data.options) ? data.options.join(',') : (data.options || '');
            row.querySelector('[name$="[options]"]').value = optionsVal;
        }

        bindRowEvents(row);
        tbody.appendChild(row);
        updateOptionsVisibility(row);
    }

    function updateOptionsVisibility(row) {
        const typeSelect = row.querySelector('.field-type-select');
        const optionsInput = row.querySelector('.options-input');
        if (typeSelect.value === 'select') {
            optionsInput.disabled = false;
            optionsInput.style.opacity = '1';
        } else {
            optionsInput.disabled = true;
            optionsInput.style.opacity = '0.4';
            optionsInput.value = '';
        }
    }

    function bindRowEvents(row) {
        row.querySelector('.remove-field-btn').addEventListener('click', function () {
            row.remove();
        });
        row.querySelector('.field-type-select').addEventListener('change', function () {
            updateOptionsVisibility(row);
        });
    }

    document.getElementById('add-field-btn').addEventListener('click', function () {
        addFieldRow(null);
    });

    // Pre-populate from old() on validation failure
    @if(old('required_fields'))
    const oldFields = @json(old('required_fields'));
    oldFields.forEach(function(f) { addFieldRow(f); });
    @endif
})();
</script>
@endsection