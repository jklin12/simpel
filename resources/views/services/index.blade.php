@extends('layouts.landing')

@section('title', 'Semua Layanan')

@section('content')
<!-- Header Section -->
<section class="bg-primary-600 pt-32 pb-16 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary-700 to-primary-500 opacity-90"></div>

    <!-- Decorative Accents -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20">
        <svg width="404" height="404" fill="none" viewBox="0 0 404 404" aria-hidden="true" class="text-white opacity-10 transform rotate-12">
            <defs>
                <pattern id="grid-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <rect x="0" y="0" width="4" height="4" fill="currentColor"></rect>
                </pattern>
            </defs>
            <rect width="404" height="404" fill="url(#grid-pattern)"></rect>
        </svg>
    </div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20">
        <svg class="w-80 h-80 text-primary-400 opacity-20" fill="currentColor" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" />
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl font-extrabold text-white sm:text-5xl mb-4 tracking-tight">Layanan Kami</h1>
        <p class="text-xl text-primary-100 max-w-2xl mx-auto font-medium">
            Daftar lengkap layanan administrasi surat menyurat yang tersedia di Kelurahan Landasan Ulin.
        </p>
    </div>
</section>

<!-- Services Grid -->
<section class="py-16 bg-gray-50 min-h-screen" x-data="{
    modalOpen: false,
    selectedService: null,
    kelurahans: {{ $kelurahans->toJson() }},
    selectedKelurahan: '',
    agreed: false,

    openModal(service) {
        this.selectedService = service;
        this.selectedKelurahan = '';
        this.agreed = false;
        this.modalOpen = true;
    },

    submitApplication() {
        if (!this.agreed) return;
        // Redirect to public application form
        window.location.href = '{{ route('permohonan.create.public') }}?service_id=' + this.selectedService.id + '&kelurahan_id=' + this.selectedKelurahan;
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($services as $service)
            <div class="group bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 transform hover:-translate-y-1">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary-600 transition-colors duration-300">
                    <span class="text-2xl font-bold text-primary-600 group-hover:text-white transition-colors">
                        {{ substr($service->kode, 0, 1) }}
                    </span>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 bg-primary-50 text-primary-700 text-xs font-semibold rounded-full border border-primary-100">
                        {{ $service->kode }}
                    </span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition-colors">
                    {{ $service->nama }}
                </h3>

                <p class="text-gray-500 text-sm mb-6 line-clamp-2 h-10">
                    {{ $service->deskripsi ?? 'Layanan pengajuan ' . $service->nama }}
                </p>

                <div class="pt-6 border-t border-gray-100">
                    <button @click="openModal({{ $service }})" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Ajukan Surat
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                <p class="text-gray-500">Belum ada layanan tersedia.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Application Modal -->
    <div
        x-show="modalOpen"
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                @click="modalOpen = false"
                aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Pengajuan <span x-text="selectedService?.nama"></span>
                            </h3>
                            <div class="mt-4 space-y-4">
                                <!-- Location Selection (Direct Kelurahan) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kelurahan / Desa</label>
                                    <select x-model="selectedKelurahan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                        <option value="">Pilih Kelurahan</option>
                                        <template x-for="kelurahan in kelurahans" :key="kelurahan.id">
                                            <option :value="kelurahan.id" x-text="kelurahan.nama"></option>
                                        </template>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Layanan ini khusus untuk wilayah Kecamatan Landasan Ulin.</p>
                                </div>

                                <!-- Terms Checkbox -->
                                <div class="relative flex items-start py-2">
                                    <div class="flex items-center h-5">
                                        <input id="terms" name="terms" type="checkbox" x-model="agreed" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms" class="font-medium text-gray-700">Syarat dan Ketentuan</label>
                                        <p class="text-gray-500">Saya menyetujui syarat dan ketentuan pengajuan surat ini dan menjamin kebenaran data yang diberikan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        @click="submitApplication()"
                        :disabled="!agreed || !selectedKelurahan"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Lanjut Pengajuan
                    </button>
                    <button
                        type="button"
                        @click="modalOpen = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection