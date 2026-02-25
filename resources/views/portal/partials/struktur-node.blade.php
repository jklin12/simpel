<li>
    <div class="org-node bg-white border border-gray-200 shadow-sm rounded-xl p-4 w-56 mx-2 relative z-10 hover:shadow-md hover:border-primary-300">
        <div class="w-16 h-16 rounded-full mx-auto mb-3 overflow-hidden border-2 border-primary-100 bg-gray-50 shadow-inner flex items-center justify-center">
            @if($node->foto)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($node->foto) }}" alt="{{ $node->nama }}" class="w-full h-full object-cover">
            @else
            <svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            @endif
        </div>
        <h4 class="font-bold text-gray-900 text-sm leading-tight mb-1">{{ $node->nama }}</h4>
        <p class="text-xs font-semibold text-primary-600 bg-primary-50 py-1 px-2 rounded-md inline-block w-full">{!! nl2br(e($node->jabatan)) !!}</p>
    </div>

    @if($node->children && $node->children->count() > 0)
    <ul>
        @foreach($node->children as $child)
        @include('portal.partials.struktur-node', ['node' => $child])
        @endforeach
    </ul>
    @endif
</li>