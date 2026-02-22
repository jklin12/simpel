@extends('layouts.landing')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<section class="relative bg-white overflow-hidden lg:pt-[110px]">
    <div class="max-w-7xl mx-auto  pt-24">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
           

            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-24 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Pelayanan Publik</span>
                        <span class="block text-primary-600 xl:inline">Mudah & Terpercaya</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Selamat datang di Portal Layanan Administrasi Kecamatan Landasan Ulin. Kami berkomitmen memberikan pelayanan prima bagi seluruh warga.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="#buat-surat" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 md:py-4 md:text-lg md:px-10 transition-transform transform hover:-translate-y-1">
                                Buat Surat Sekarang
                            </a>
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-3">
                            <a href="#cek-status" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 md:py-4 md:text-lg md:px-10 transition-transform transform hover:-translate-y-1">
                                Cek Status
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 mt-8 lg:mt-0">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="{{ asset('images/banner.png') }}" alt="Pelayanan Publik">
    </div>
</section>

<!-- Alur Permohonan Section -->
<section class="py-16 bg-gray-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-base text-primary-600 font-semibold tracking-wide uppercase">Alur Proses</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Mudahnya Mengurus Surat
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                Ikuti langkah-langkah sederhana berikut untuk mengajukan permohonan surat secara online.
            </p>
        </div>

        <div class="relative">
            <img src="{{ asset('images/infographic.png') }}" alt="Infografis Alur" class="w-full rounded-2xl shadow-xl transform hover:scale-[1.01] transition-transform duration-300">
        </div>
    </div>
</section>

<!-- Jenis Surat Section -->
<section id="buat-surat" class="py-16 bg-white" x-data="{
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
        if (!this.agreed || !this.selectedKelurahan) return;
        window.location.href = '{{ route('permohonan.create.public') }}?service_id=' + this.selectedService.id + '&kelurahan_id=' + this.selectedKelurahan;
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-base text-primary-600 font-semibold tracking-wide uppercase">Layanan Kami</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Jenis Layanan Surat
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                Pilih jenis surat yang Anda butuhkan dan ajukan permohonan dengan cepat.
            </p>
        </div>

        @php
        $cardColors = [
            'SKTM'  => ['bg' => 'bg-blue-50',   'hover' => 'group-hover:bg-blue-600',   'text' => 'text-blue-600',   'btn' => 'text-blue-600 hover:text-blue-700'],
            'SKTMR' => ['bg' => 'bg-orange-50',  'hover' => 'group-hover:bg-orange-600', 'text' => 'text-orange-600', 'btn' => 'text-orange-600 hover:text-orange-700'],
            'SKBM'  => ['bg' => 'bg-purple-50',  'hover' => 'group-hover:bg-purple-600', 'text' => 'text-purple-600', 'btn' => 'text-purple-600 hover:text-purple-700'],
        ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($featuredServices as $service)
            @php $color = $cardColors[$service->kode] ?? ['bg' => 'bg-primary-50', 'hover' => 'group-hover:bg-primary-600', 'text' => 'text-primary-600', 'btn' => 'text-primary-600 hover:text-primary-700']; @endphp
            <div class="group relative bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col">
                <div class="w-14 h-14 {{ $color['bg'] }} rounded-xl flex items-center justify-center mb-6 {{ $color['hover'] }} transition-colors">
                    <svg class="w-8 h-8 {{ $color['text'] }} group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-semibold rounded mb-2 w-fit">{{ $service->kode }}</span>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $service->nama }}</h3>
                <p class="text-gray-500 text-sm mb-6 flex-1">{{ $service->deskripsi }}</p>
                <button
                    @click="openModal({{ $service->toJson() }})"
                    class="inline-flex items-center {{ $color['btn'] }} font-semibold transition-colors">
                    Ajukan Sekarang
                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
            </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Lihat Semua Layanan
            </a>
        </div>
    </div>

    <!-- Modal Pilih Kelurahan -->
    <div
        x-show="modalOpen"
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
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

            <!-- Modal Panel -->
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Pengajuan <span x-text="selectedService?.nama"></span>
                            </h3>
                            <div class="mt-4 space-y-4">
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

                                <div class="relative flex items-start py-2">
                                    <div class="flex items-center h-5">
                                        <input id="home-terms" name="terms" type="checkbox" x-model="agreed" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="home-terms" class="font-medium text-gray-700">Syarat dan Ketentuan</label>
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

<!-- FAQ Section -->
<section id="faq" class="py-16 bg-primary-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-base text-primary-600 font-semibold tracking-wide uppercase">FAQ</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Pertanyaan Populer
            </p>
        </div>

        <div class="space-y-4" x-data="{ active: null }">
            <!-- FAQ 1 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <button @click="active = (active === 1 ? null : 1)" class="w-full px-6 py-4 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                    <span class="font-semibold text-gray-900">Berapa lama proses pembuatan surat?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" :class="active === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="active === 1" x-collapse class="px-6 pb-4 text-gray-600">
                    Proses pembuatan surat rata-rata memakan waktu 1-2 hari kerja setelah persyaratan dinyatakan lengkap.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <button @click="active = (active === 2 ? null : 2)" class="w-full px-6 py-4 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                    <span class="font-semibold text-gray-900">Apakah harus datang ke kantor kelurahan?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" :class="active === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="active === 2" x-check class="px-6 pb-4 text-gray-600">
                    Tidak selalu. Anda dapat mengajukan permohonan secara online. Namun, untuk pengambilan surat fisik (jika diperlukan tanda tangan basah), Anda mungkin perlu datang atau menggunakan layanan pengiriman jika tersedia.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <button @click="active = (active === 3 ? null : 3)" class="w-full px-6 py-4 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                    <span class="font-semibold text-gray-900">Apa saja dokumen yang perlu disiapkan?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" :class="active === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="active === 3" x-collapse class="px-6 pb-4 text-gray-600">
                    Dokumen dasar biasanya meliputi scan KTP dan Kartu Keluarga. Persyaratan tambahan tergantung jenis surat yang diajukan.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<div class="bg-primary-700">
    <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
            <span class="block">Siap untuk mengajukan surat?</span>
            <span class="block text-primary-200">Mulai sekarang juga, cepat dan mudah.</span>
        </h2>
        <p class="mt-4 text-lg leading-6 text-primary-100">
            Daftar akun sekarang untuk menikmati layanan administrasi digital tanpa antre.
        </p>
    </div>
</div>

@include('components.success-modal')
@endsection