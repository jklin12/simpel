@extends('layouts.landing')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<section class="relative bg-white overflow-hidden lg:pt-[110px]">
    <div class="max-w-7xl mx-auto  pt-24">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>

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
<section id="buat-surat" class="py-16 bg-white">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Card 1 -->
            <div class="group relative bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="w-14 h-14 bg-primary-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary-600 transition-colors">
                    <svg class="w-8 h-8 text-primary-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Surat Keterangan Domisili</h3>
                <p class="text-gray-500 text-sm mb-4">Untuk keperluan administrasi kependudukan dan tempat tinggal.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center text-primary-600 font-semibold hover:text-primary-700">
                    Ajukan Sekarang <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <!-- Card 2 -->
            <div class="group relative bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-orange-600 transition-colors">
                    <svg class="w-8 h-8 text-orange-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">SKCK Pengantar</h3>
                <p class="text-gray-500 text-sm mb-4">Surat pengantar untuk pembuatan SKCK di kepolisian.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center text-orange-600 font-semibold hover:text-orange-700">
                    Ajukan Sekarang <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <!-- Card 3 -->
            <div class="group relative bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-green-600 transition-colors">
                    <svg class="w-8 h-8 text-green-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Surat Kematian</h3>
                <p class="text-gray-500 text-sm mb-4">Laporan dan keterangan kematian warga untuk akta kematian.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center text-green-600 font-semibold hover:text-green-700">
                    Ajukan Sekarang <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <!-- Card 4 -->
            <div class="group relative bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-purple-600 transition-colors">
                    <svg class="w-8 h-8 text-purple-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Surat Usaha (SKU)</h3>
                <p class="text-gray-500 text-sm mb-4">Keterangan memiliki usaha untuk keperluan bank atau izin.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700">
                    Ajukan Sekarang <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Lihat Semua Layanan
            </a>
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