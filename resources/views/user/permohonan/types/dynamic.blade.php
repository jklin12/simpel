{{-- Dynamic form template for jenis surat with required_fields defined via admin UI --}}

@php
    $textFields = collect($fields)->filter(fn($f) => ($f['type'] ?? 'text') !== 'file');
    $fileFields = collect($fields)->filter(fn($f) => ($f['type'] ?? 'text') === 'file');
@endphp

@php $textChunks = $textFields->chunk(6)->values(); @endphp
@foreach($textChunks as $chunkIndex => $chunk)
<div data-wizard-step data-title="{{ $textChunks->count() > 1 ? 'Data permohonan (' . ($chunkIndex + 1) . '/' . $textChunks->count() . ')' : 'Data permohonan' }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8 mb-8">
    <h2 class="text-lg font-bold text-gray-900 mb-5">Data Permohonan@if($textChunks->count() > 1) <span class="text-sm font-normal text-gray-400">({{ $chunkIndex + 1 }}/{{ $textChunks->count() }})</span>@endif</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($chunk as $field)
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
@endforeach

@if($fileFields->isNotEmpty())
<div data-wizard-step data-title="Berkas" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8 mb-8">
    <h2 class="text-lg font-bold text-gray-900 mb-2">Berkas Pendukung</h2>
    <p class="text-sm text-gray-500 mb-5">Format yang diterima: JPG, PNG, PDF. Maksimal 5 MB per file.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($fileFields as $field)
            @php
                $name = $field['name'];
                $label = $field['label'];
                $isRequired = $field['is_required'] ?? false;
                $requiredAttr = $isRequired ? 'required' : '';
            @endphp
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $label }}<span class="text-red-500 ml-1">*</span>
                </label>
                <input type="file" name="{{ $name }}" accept=".jpg,.jpeg,.png,.pdf" required
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endforeach
    </div>
</div>
@endif
