{{-- Satu simpul struktur organisasi (rekursif). $node = PortalStrukturOrganisasi dengan relasi children, $depth = kedalaman (0 = akar). --}}
@php
    $punyaAnak = $node->children && $node->children->count() > 0;
    $avatar = $depth === 0 ? 'w-14 h-14' : 'w-11 h-11';
@endphp

<li @if($punyaAnak) x-data="{ open: {{ $depth === 0 ? 'true' : 'false' }} }" @endif>
    @if($punyaAnak)
    <button type="button" @click="open = !open" :aria-expanded="open"
        class="w-full flex items-center gap-3 text-left bg-white border border-gray-200 border-l-4 border-l-primary-600 rounded-xl p-3 min-h-[64px]">
    @else
    <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-3 min-h-[64px]">
    @endif
        <span class="{{ $avatar }} rounded-full bg-gray-100 border-2 border-white shadow-sm overflow-hidden flex-none flex items-center justify-center">
            @if($node->foto)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($node->foto) }}" alt="{{ $node->nama }}" loading="lazy" class="w-full h-full object-cover">
            @else
            <svg class="w-2/3 h-2/3 text-gray-300" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            @endif
        </span>
        <span class="flex-1 min-w-0">
            <span class="block text-[11px] font-semibold text-primary-700 uppercase tracking-wide leading-snug">{!! nl2br(e($node->jabatan)) !!}</span>
            <span class="block text-[14px] font-bold text-gray-900 leading-snug mt-0.5">{{ $node->nama ?: '-' }}</span>
        </span>
        @if($punyaAnak)
        <span class="flex-none flex items-center gap-1 text-[11px] text-gray-400">
            {{ $node->children->count() }}
            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
        </span>
        @endif
    @if($punyaAnak)
    </button>
    @else
    </div>
    @endif

    @if($punyaAnak)
    <ul x-show="open" x-cloak class="mt-2 ml-4 pl-3 border-l-2 border-primary-100 space-y-2">
        @foreach($node->children as $child)
        @include('portal.partials.struktur-node-mobile', ['node' => $child, 'depth' => $depth + 1])
        @endforeach
    </ul>
    @endif
</li>
