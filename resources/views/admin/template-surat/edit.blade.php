@extends('layouts.app')

@section('title', 'Edit Template Surat')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.template-surat.index') }}" class="hover:text-blue-600">Template Surat</a>
        <span>/</span>
        <span class="text-gray-800">Edit</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-800">Edit Template Surat</h1>
</div>

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start gap-3">
    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
    </svg>
    <div>
        <p class="font-semibold">Error!</p>
        <p class="text-sm">{{ session('error') }}</p>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
    <p class="font-semibold mb-2">Terdapat kesalahan pada form:</p>
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
    <form action="{{ route('admin.template-surat.update', $template->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-6">

            {{-- Jenis Surat --}}
            <div>
                <label for="jenis_surat_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat <span class="text-red-500">*</span></label>
                <select name="jenis_surat_id" id="jenis_surat_id"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                    <option value="">-- Pilih Jenis Surat --</option>
                    @foreach($jenisSurats as $js)
                    <option value="{{ $js->id }}" @selected(old('jenis_surat_id', $template->jenis_surat_id) == $js->id)>{{ $js->nama }}</option>
                    @endforeach
                </select>
                @error('jenis_surat_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Judul --}}
            <div>
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Template <span class="text-red-500">*</span></label>
                <input type="text" name="judul" id="judul"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    value="{{ old('judul', $template->judul) }}" required>
                @error('judul')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    placeholder="Deskripsi singkat...">{{ old('deskripsi', $template->deskripsi) }}</textarea>
                @error('deskripsi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- File Upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File Template</label>
                {{-- Current file --}}
                <div class="flex items-center gap-2 mb-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <a href="{{ $template->file_url }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                        {{ $template->file_original_name ?? basename($template->file_path) }}
                    </a>
                    <span class="text-xs text-gray-400">(file saat ini)</span>
                </div>
                <input type="file" name="file" id="file"
                    accept=".pdf,.doc,.docx"
                    class="w-full text-sm text-gray-500 border border-gray-300 rounded-lg px-3 py-2 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-xs text-gray-400">Biarkan kosong jika tidak ingin mengganti file. Format: PDF, DOC, DOCX. Maks. 10 MB.</p>
                @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Status Aktif --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" value="1" name="is_active" id="is_active"
                    class="rounded border-gray-300 text-blue-600 shadow-sm"
                    {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">Tampilkan di halaman publik (aktif)</label>
            </div>

            {{-- Syarat Builder --}}
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Syarat-Syarat</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar syarat yang harus dipenuhi untuk menggunakan template ini.</p>
                    </div>
                    <button type="button" id="add-syarat-btn"
                        class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-medium transition shadow-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Syarat
                    </button>
                </div>
                <div id="syarat-list" class="space-y-2"></div>
                <p class="text-xs text-gray-400 mt-2">Tiap baris = satu syarat.</p>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.template-surat.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const list = document.getElementById('syarat-list');

        function addSyaratRow(value) {
            const row = document.createElement('div');
            row.className = 'syarat-row flex items-center gap-2';
            row.innerHTML = `
                <input type="text" name="syarat[]" value="${value || ''}"
                    placeholder="Contoh: Fotokopi KTP yang masih berlaku"
                    class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                <button type="button" class="remove-syarat-btn text-red-500 hover:text-red-700 p-1 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            row.querySelector('.remove-syarat-btn').addEventListener('click', () => row.remove());
            list.appendChild(row);
        }

        document.getElementById('add-syarat-btn').addEventListener('click', () => addSyaratRow(''));

        // Pre-populate existing syarat
        const existing = <?php echo json_encode(old('syarat', $template->syarat ?? [])); ?>;
        existing.forEach(s => addSyaratRow(s));
    })();
</script>
@endpush