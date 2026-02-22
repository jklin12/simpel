{{-- Dynamic form template for jenis surat with required_fields defined via admin UI --}}

@php
    $textFields = collect($fields)->filter(fn($f) => ($f['type'] ?? 'text') !== 'file');
    $fileFields = collect($fields)->filter(fn($f) => ($f['type'] ?? 'text') === 'file');
@endphp

@if($textFields->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8">
    <div class="flex items-center gap-3 mb-6">
        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">1</span>
        <h2 class="text-lg font-bold text-gray-900">Data Permohonan</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($textFields as $field)
            @php
                $name = $field['name'];
                $label = $field['label'];
                $type = $field['type'] ?? 'text';
                $isRequired = $field['is_required'] ?? false;
                $options = $field['options'] ?? [];
                $requiredAttr = $isRequired ? 'required' : '';
            @endphp

            @if($type === 'textarea')
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $label }}@if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                    </label>
                    <textarea name="{{ $name }}" rows="3" {{ $requiredAttr }}
                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">{{ old($name) }}</textarea>
                    @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @elseif($type === 'select' && !empty($options))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $label }}@if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                    </label>
                    <select name="{{ $name }}" {{ $requiredAttr }}
                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                        <option value="">-- Pilih {{ $label }} --</option>
                        @foreach($options as $option)
                            <option value="{{ $option }}" {{ old($name) == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @elseif($type === 'date')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $label }}@if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                    </label>
                    <input type="date" name="{{ $name }}" value="{{ old($name) }}" {{ $requiredAttr }}
                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @elseif($type === 'number')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $label }}@if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                    </label>
                    <input type="number" name="{{ $name }}" value="{{ old($name) }}" {{ $requiredAttr }}
                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $label }}@if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                    </label>
                    <input type="text" name="{{ $name }}" value="{{ old($name) }}" {{ $requiredAttr }}
                        class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 focus:border-primary-500 transition-colors py-3 px-4">
                    @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
        @endforeach
    </div>
</div>
@endif

@if($fileFields->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8">
    <div class="flex items-center gap-3 mb-6">
        <span class="w-8 h-8 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">2</span>
        <h2 class="text-lg font-bold text-gray-900">Berkas Pendukung</h2>
    </div>
    <p class="text-sm text-gray-500 mb-6">Format yang diterima: JPG, PNG, PDF. Maksimal 5 MB per file.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($fileFields as $field)
            @php
                $name = $field['name'];
                $label = $field['label'];
                $isRequired = $field['is_required'] ?? false;
                $requiredAttr = $isRequired ? 'required' : '';
            @endphp
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $label }}@if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                </label>
                <input type="file" name="{{ $name }}" accept=".jpg,.jpeg,.png,.pdf" {{ $requiredAttr }}
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endforeach
    </div>
</div>
@endif
