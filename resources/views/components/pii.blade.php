@props([
    'value' => null,
    'field',
    'source' => 'permohonan',
    'subjectId' => null,
    'type' => null,
    'reveal' => true,   // izinkan tombol reveal bila user berhak
])

@php
    use App\Support\Pii;

    $piiType   = $type ?: Pii::inferType($field);
    $masked    = Pii::mask($value, $piiType);
    $hasValue  = trim((string) $value) !== '';
    $canReveal = $reveal
        && $hasValue
        && auth()->check()
        && auth()->user()->hasAnyRole((array) config('pii.reveal_roles', ['super_admin']));
@endphp

@if($canReveal)
    <span
        {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5']) }}
        x-data="{
            shown: false,
            loading: false,
            full: '',
            masked: @js($masked),
            async toggle() {
                if (this.shown) { this.shown = false; return; }
                if (this.full !== '') { this.shown = true; return; }
                this.loading = true;
                try {
                    const res = await fetch(@js(route('admin.pii.reveal')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': @js(csrf_token()),
                        },
                        body: JSON.stringify({
                            source: @js($source),
                            id: @js($subjectId),
                            field: @js($field),
                        }),
                    });
                    if (!res.ok) throw new Error('reveal-failed');
                    const data = await res.json();
                    this.full = data.value ?? '-';
                    this.shown = true;
                } catch (e) {
                    alert('Gagal menampilkan data. Coba lagi.');
                } finally {
                    this.loading = false;
                }
            }
        }"
    >
        <span x-text="shown ? full : masked"></span>
        <button
            type="button"
            @click="toggle()"
            :disabled="loading"
            class="text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded border border-[#dce1ff] text-[#1d4ed8] hover:bg-[#eff6ff] transition-colors disabled:opacity-50"
            :title="shown ? 'Sembunyikan' : 'Tampilkan data asli'"
        >
            <span x-show="!loading" x-text="shown ? 'Sembunyikan' : 'Tampilkan'"></span>
            <span x-show="loading" x-cloak>…</span>
        </button>
    </span>
@else
    <span {{ $attributes }}>{{ $masked }}</span>
@endif
