@extends('layouts.landing')

@section('title', 'Form Pengajuan Surat')

@section('content')
@php
    // Wizard bertahap: jenis surat statis hanya bila terdaftar di config/layanan.php (wizard_aktif);
    // jenis dinamis (required_fields dari admin) mengikuti wizard_dinamis.
    $punyaPartial = View::exists('user.permohonan.types.' . strtolower($service->kode));
    $wizard = $punyaPartial
        ? in_array(strtolower($service->kode), config('layanan.wizard_aktif', []), true)
        : (config('layanan.wizard_dinamis', true) && !empty($service->required_fields));
@endphp
<div class="bg-gray-50 min-h-screen pt-4 {{ $wizard ? 'pb-32' : 'pb-16' }}" @if($wizard) x-data="permohonanWizard()" @keydown.enter="onEnter($event)" @endif>

    @if($wizard)
    {{-- Header wizard: nama surat, langkah n dari N, progress bar (menempel di bawah navbar) --}}
    <div class="sticky top-14 lg:top-16 z-30 bg-white border-b border-gray-200 px-4 py-3" x-cloak>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between gap-3">
                <p class="text-[13px] font-semibold text-gray-900 truncate">{{ $service->nama }}</p>
                <p class="text-xs text-gray-500 whitespace-nowrap" x-text="'Langkah ' + (current + 1) + ' dari ' + total"></p>
            </div>
            <div class="mt-2 h-1.5 rounded-full bg-gray-100 overflow-hidden" role="progressbar" :aria-valuenow="current + 1" aria-valuemin="1" :aria-valuemax="total">
                <div class="h-full bg-primary-600 rounded-full transition-all duration-300" :style="`width: ${((current + 1) / total) * 100}%`"></div>
            </div>
            <p class="mt-2 text-[15px] font-bold text-primary-700" x-text="title"></p>
        </div>
    </div>
    @endif

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 {{ $wizard ? 'mt-4' : '' }}">
        <!-- Header Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-8 mb-6 md:mb-8" @if($wizard) x-show="current === 0" @endif>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600 font-bold text-xl flex-none">
                    {{ substr($service->kode, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900 leading-snug">{{ $service->nama }}</h1>
                    <p class="text-sm text-gray-500">{{ $kelurahan->nama }}, Kecamatan {{ $kelurahan->kecamatan->nama ?? 'Landasan Ulin' }}</p>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
                <p class="text-sm text-blue-700">
                    <strong>Penting:</strong> Pastikan seluruh data yang diisi sesuai dengan dokumen asli (KTP/KK). Kesalahan data dapat menyebabkan permohonan ditolak.
                </p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('layanan.surat.store') }}" method="POST" class="space-y-8" enctype="multipart/form-data"
            @if($wizard) novalidate @submit="onSubmit($event)" @endif>
            @csrf
            <input type="hidden" name="jenis_surat_id" value="{{ $service->id }}">
            <input type="hidden" name="kelurahan_id" value="{{ $kelurahan->id }}">

            @if($wizard && $errors->any())
            {{-- Berkas yang dipilih sebelum redirect error tidak bisa dipulihkan peramban --}}
            <div x-show="stepHasFile(current) || isReview" x-cloak class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">
                <strong>Berkas perlu diunggah ulang.</strong> Peramban tidak menyimpan berkas yang sebelumnya dipilih. Pilih kembali setiap berkas pada langkah Berkas.
            </div>
            @endif

            <!-- Dynamic Fields Based on Letter Type -->
            @if(View::exists('user.permohonan.types.' . strtolower($service->kode)))
            @include('user.permohonan.types.' . strtolower($service->kode))
            @elseif($service->required_fields && count($service->required_fields) > 0)
            @include('user.permohonan.types.dynamic', ['fields' => $service->required_fields])
            @endif

            @if($wizard)
            {{-- Langkah terakhir: Tinjau & Kirim --}}
            <div x-show="isReview" x-cloak class="space-y-4">
                <div class="rounded-lg bg-blue-50 border border-blue-100 p-3 text-sm text-blue-700">
                    Periksa kembali data Anda. Tekan <strong>Ubah</strong> pada bagian yang perlu diperbaiki, lalu kirim permohonan.
                </div>
                <template x-for="section in summary" :key="section.index">
                    <div class="bg-white rounded-2xl border border-gray-100 p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[15px] font-bold text-gray-900" x-text="section.title"></h3>
                            <button type="button" @click="show(section.index)" class="h-11 px-3 -mr-2 text-sm font-semibold text-primary-600">Ubah</button>
                        </div>
                        <dl class="divide-y divide-gray-100">
                            <template x-for="row in section.rows" :key="row.label + row.index">
                                <div class="py-2">
                                    <dt class="text-[11px] uppercase tracking-wide font-semibold text-gray-400" x-text="row.label"></dt>
                                    <dd class="text-sm mt-0.5 break-words"
                                        :class="row.value ? 'text-gray-900 font-medium' : ((row.isFile && row.required) ? 'text-red-600' : 'text-gray-400')"
                                        x-text="row.value || (row.isFile ? (row.required ? 'Belum dipilih' : 'Tidak dilampirkan') : '-')"></dd>
                                </div>
                            </template>
                        </dl>
                    </div>
                </template>
            </div>

            {{-- Bottom bar tetap: Kembali / Lanjut (Kirim di langkah terakhir) --}}
            <div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-gray-200 px-4 py-3" x-cloak>
                <div class="max-w-4xl mx-auto">
                    <p x-show="errorMsg" x-text="errorMsg" class="mb-2 text-sm text-red-600 text-center" role="alert"></p>
                    <div class="flex gap-3">
                        <button type="button" @click="prev()" class="h-12 px-5 rounded-xl border border-gray-300 bg-white text-gray-700 font-semibold">Kembali</button>
                        <button type="button" x-show="!isReview" @click="next()" class="flex-1 h-12 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold">Lanjut</button>
                        <button type="submit" x-show="isReview" :disabled="submitting" class="flex-1 h-12 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold disabled:opacity-60 disabled:cursor-not-allowed">
                            <span x-text="submitting ? 'Mengirim...' : 'Kirim Permohonan'"></span>
                        </button>
                    </div>
                </div>
            </div>
            @else
            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <a href="{{ route('layanan.index') }}" class="mr-4 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Kembali
                </a>
                <button type="submit" class="px-8 py-3 bg-primary-600 rounded-lg text-white font-bold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transition-transform transform hover:-translate-y-1">
                    Kirim Permohonan
                </button>
            </div>
            @endif
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control {
        border-radius: 0.5rem;
        border-color: #d1d5db;
        background-color: #f9fafb;
        padding: 0.75rem 1rem;
        font-family: inherit;
        font-size: 1rem;
        line-height: 1.5rem;
        min-height: 50px;
        transition: background-color 0.2s, border-color 0.2s;
    }

    .ts-control.focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }

    .ts-wrapper.single .ts-control:after {
        right: 1rem;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectPekerjaanAll = document.querySelectorAll('.select2-pekerjaan');
        selectPekerjaanAll.forEach(function(selectPekerjaan) {
            if (selectPekerjaan && !selectPekerjaan.tomselect) {
                new TomSelect(selectPekerjaan, {
                    create: true,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: "Pilih atau Ketik Pekerjaan Baru"
                });
            }
        });

        // Inject attachment guides dari database ke bawah setiap file input
        const storageBase = '<?= Storage::url("") ?>';
        const attachmentGuides = <?= json_encode($service->attachment_guides ?? []) ?>;
        document.querySelectorAll('input[type="file"]:not([x-ref])').forEach(function(input) {
            const fieldName = input.getAttribute('name');
            if (!fieldName || !attachmentGuides[fieldName]) return;
            const guide = attachmentGuides[fieldName];
            const wrapper = document.createElement('div');
            wrapper.className = 'mt-2 rounded-lg bg-amber-50 border border-amber-100 px-3 py-2 text-xs text-amber-800 space-y-1';
            if (guide.keterangan) {
                const p1 = document.createElement('p');
                p1.innerHTML = '<span class="font-semibold">&#x1F4CB; Keterangan:</span> ' + guide.keterangan;
                wrapper.appendChild(p1);
            }
            if (guide.contoh_file) {
                const fileUrl = storageBase + guide.contoh_file;
                const ext = guide.contoh_file.split('.').pop().toLowerCase();
                const isImg = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
                const p2 = document.createElement('div');
                p2.className = 'flex items-start gap-2 mt-1';
                p2.innerHTML = '<span class="font-semibold whitespace-nowrap">&#x1F4CE; Contoh:</span> ' +
                    (isImg ?
                        `<a href="${fileUrl}" target="_blank"><img src="${fileUrl}" class="h-16 rounded border border-amber-200 object-contain cursor-pointer hover:opacity-80" alt="Contoh dokumen"></a>` :
                        `<a href="${fileUrl}" target="_blank" class="underline text-amber-700 hover:text-amber-900">Lihat contoh dokumen &#x2197;</a>`);
                wrapper.appendChild(p2);
            }
            input.parentNode.insertBefore(wrapper, input.nextSibling);
        });
    });
</script>
@if($wizard)
<script>
    /**
     * Wizard bertahap: setiap elemen [data-wizard-step] di dalam <form> menjadi satu langkah,
     * ditambah satu langkah "Tinjau & Kirim" yang dibuat otomatis dari isi field.
     * Validasi dilakukan per langkah (form memakai novalidate, input required tidak disembunyikan
     * dengan display:none sebelum divalidasi), dan seluruh langkah divalidasi ulang saat kirim.
     */
    function permohonanWizard() {
        return {
            current: 0,
            steps: [],
            titles: [],
            summary: [],
            errorMsg: '',
            submitting: false,

            get total() { return this.steps.length + 1; },
            get isReview() { return this.steps.length > 0 && this.current === this.steps.length; },
            get title() { return this.isReview ? 'Tinjau & Kirim' : (this.titles[this.current] || ''); },

            init() {
                this.steps = Array.from(this.$el.querySelectorAll('[data-wizard-step]'));
                this.titles = this.steps.map(s => s.dataset.title || 'Langkah');
                // Setelah redirect error dari server: buka langkah pertama yang berisi pesan error.
                const errIdx = this.steps.findIndex(s => s.querySelector('p.text-red-600'));
                this.show(errIdx >= 0 ? errIdx : 0, false);
            },

            show(i, scroll = true) {
                this.current = i;
                this.errorMsg = '';
                this.steps.forEach((s, k) => { s.hidden = (k !== i); });
                if (this.isReview) this.buildSummary();
                if (scroll) window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            stepHasFile(i) {
                const s = this.steps[i];
                return !!(s && s.querySelector('input[type="file"][name]'));
            },

            // Field di langkah ini yang ikut divalidasi/diringkas (abaikan yang disembunyikan kondisional oleh x-show).
            fieldsOf(step) {
                return Array.from(step.querySelectorAll('input, select, textarea'))
                    .filter(el => el.name && el.type !== 'hidden' && el.type !== 'submit' && !this.condHidden(el, step));
            },
            condHidden(el, step) {
                for (let p = el.parentElement; p && p !== step; p = p.parentElement) {
                    if (getComputedStyle(p).display === 'none') return true;
                }
                return false;
            },
            labelOf(el) {
                const wrap = el.closest('div');
                const lab = wrap ? wrap.querySelector('label') : null;
                return lab ? lab.textContent.replace(/\*/g, '').replace(/\s+/g, ' ').trim() : (el.name || '').replace(/_/g, ' ');
            },

            validateStep(i) {
                const step = this.steps[i];
                for (const el of this.fieldsOf(step)) {
                    if (!el.checkValidity()) {
                        this.show(i, false);
                        this.errorMsg = 'Lengkapi dulu: ' + this.labelOf(el);
                        this.$nextTick(() => { el.scrollIntoView({ block: 'center', behavior: 'smooth' }); el.reportValidity(); });
                        return false;
                    }
                }
                return true;
            },

            next() {
                if (this.isReview) return;
                if (!this.validateStep(this.current)) return;
                this.show(this.current + 1);
            },
            prev() {
                if (this.current > 0) this.show(this.current - 1);
                else window.location.href = @json(route('layanan.index'));
            },

            // Enter pada input tidak boleh mengirim form sebelum langkah terakhir.
            onEnter(e) {
                const t = e.target;
                if (t.tagName === 'TEXTAREA' || t.tagName === 'BUTTON') return;
                if (t.tagName === 'INPUT' || t.tagName === 'SELECT') {
                    e.preventDefault();
                    this.next();
                }
            },

            onSubmit(e) {
                if (!this.isReview) { e.preventDefault(); this.next(); return; }
                for (let i = 0; i < this.steps.length; i++) {
                    if (!this.validateStep(i)) { e.preventDefault(); return; }
                }
                this.submitting = true;
            },

            buildSummary() {
                this.summary = this.steps.map((s, i) => ({
                    index: i,
                    title: this.titles[i],
                    rows: this.fieldsOf(s).map((el, k) => this.rowOf(el, k)).filter(Boolean),
                }));
            },
            rowOf(el, k) {
                let value = '';
                const isFile = el.type === 'file';
                if (isFile) value = el.files && el.files.length ? el.files[0].name : '';
                else if (el.tagName === 'SELECT') value = el.value ? (el.selectedOptions[0] ? el.selectedOptions[0].text : el.value) : '';
                else if (el.type === 'radio' || el.type === 'checkbox') { if (!el.checked) return null; value = el.value; }
                else value = el.value;
                return { label: this.labelOf(el), value: (value || '').trim(), isFile, required: el.required, index: k };
            },
        };
    }
</script>
@endif
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#2563eb',
        });
    });
</script>
@endif
@endsection
