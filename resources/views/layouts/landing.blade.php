<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - SiMPEL Landasan Ulin</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Analytics (Firebase) -->
    @if(config('services.firebase.measurement_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.firebase.measurement_id') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ config('services.firebase.measurement_id') }}');
    </script>
    @endif

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb', // Blue
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        secondary: {
                            400: '#facc15',
                            500: '#eab308',
                            600: '#ca8a04',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">

    @php
        $navItems = [
            ['Beranda', route('home'), request()->routeIs('home'), 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zM9 22V12h6v10'],
            ['Daftar Layanan Surat', route('layanan.index'), request()->routeIs('layanan.index'), 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8'],
            ['Cek Status Permohonan', route('layanan.surat.tracking'), request()->routeIs('layanan.surat.tracking*'), 'M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11'],
            ['Download Template Surat', route('layanan.surat.template.index'), request()->routeIs('layanan.surat.template.*'), 'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3'],
            ['Struktur Organisasi', route('struktur-organisasi'), request()->routeIs('struktur-organisasi*'), 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
            ['Berita & Pengumuman', route('berita.index'), request()->routeIs('berita.*'), 'M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2M18 14h-8M15 18h-5M10 6h8v4h-8z'],
            ['Peta Wilayah', route('peta.index'), request()->routeIs('peta.*'), 'M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4zM8 2v16M16 6v16'],
            ['FAQ', route('faq'), request()->routeIs('faq'), 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20zM9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01'],
        ];
    @endphp

    <!-- Topbar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-200"
        x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 lg:h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2.5">
                    <img src="{{ asset('images/logo_simpel.png') }}" alt="SiMPEL" class="h-8 lg:h-10 w-auto object-contain">
                    <span class="leading-none">
                        <span class="block text-base lg:text-xl font-bold text-gray-900">SiMPEL</span>
                        <span class="block text-[10px] text-gray-500 font-medium tracking-wider uppercase mt-0.5">Landasan Ulin</span>
                    </span>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-base font-medium hover:text-primary-600 transition-colors {{ request()->routeIs('home') ? 'text-primary-600' : 'text-gray-700' }}">Beranda</a>
                    <a href="{{ route('berita.index') }}" class="text-base font-medium hover:text-primary-600 transition-colors {{ request()->routeIs('berita.*') ? 'text-primary-600' : 'text-gray-700' }}">Berita</a>
                    <a href="{{ route('peta.index') }}" class="text-base font-medium hover:text-primary-600 transition-colors {{ request()->routeIs('peta.*') ? 'text-primary-600' : 'text-gray-700' }}">Peta Wilayah</a>
                    <a href="{{ route('struktur-organisasi') }}" class="text-base font-medium hover:text-primary-600 transition-colors {{ request()->routeIs('struktur-organisasi*') ? 'text-primary-600' : 'text-gray-700' }}">Struktur Organisasi</a>

                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors focus:outline-none">
                            Layanan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                <a href="{{ route('layanan.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary-600" role="menuitem">Ajukan Surat</a>
                                <a href="{{ route('layanan.surat.tracking') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary-600" role="menuitem">Cek Status</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('layanan.surat.template.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary-600" role="menuitem">Download Template</a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('faq') }}" class="text-sm font-medium hover:text-primary-600 transition-colors {{ request()->routeIs('faq') ? 'text-primary-600' : 'text-gray-600' }}">FAQ</a>
                    <a href="#contact" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">Contact Us</a>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="flex lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                        class="w-11 h-11 -mr-2 flex items-center justify-center text-gray-700 focus:outline-none"
                        :aria-expanded="mobileMenuOpen" aria-label="Menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (drawer) -->
        <div x-show="mobileMenuOpen" x-cloak @click.outside="mobileMenuOpen = false"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden absolute left-0 right-0 top-full bg-white border-b border-gray-200 shadow-lg max-h-[calc(100vh-3.5rem)] overflow-y-auto">
            <div class="py-1">
                @foreach($navItems as [$label, $href, $active, $iconPath])
                <a href="{{ $href }}" class="flex items-center gap-3 px-4 h-12 text-[15px] {{ $active ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-none {{ $active ? 'text-primary-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $iconPath }}" /></svg>
                    {{ $label }}
                </a>
                @endforeach
            </div>
            <div class="px-4 pt-2 pb-4 space-y-2">
                <a href="{{ route('layanan.index') }}" class="flex items-center justify-center h-12 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700">Ajukan Surat</a>
                @auth
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center h-11 rounded-xl text-sm font-medium text-primary-600 bg-primary-50">Dashboard</a>
                @else
                <a href="{{ route('login') }}" class="flex items-center justify-center h-11 rounded-xl text-sm font-medium text-primary-600 bg-primary-50">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-100 mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 lg:pt-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand + kontak -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-3">
                        <img src="{{ asset('images/logo_simpel.png') }}" alt="SiMPEL" class="h-8 w-auto object-contain">
                        <span class="text-base font-bold text-gray-900">SiMPEL Landasan Ulin</span>
                    </div>
                    <p class="text-gray-500 text-[13px] leading-relaxed mb-4">
                        Sistem Informasi Pelayanan Administrasi Kecamatan Landasan Ulin. Memudahkan masyarakat dalam pengurusan surat menyurat secara digital.
                    </p>
                    <ul id="contact" class="space-y-2.5">
                        <li class="flex items-start gap-3 text-[13px] text-gray-600">
                            <svg class="w-4 h-4 text-primary-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Jl. Kenanga, RT.06/RW.09, Kelurahan Landasan Ulin Timur, Kecamatan Landasan Ulin, Kota Banjarbaru, Kalimantan Selatan, kode pos 70724
                        </li>
                        <li class="flex items-center gap-3 text-[13px] text-gray-600">
                            <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            kecamatan@landasanulin.id
                        </li>
                    </ul>
                </div>

                <!-- Tautan: accordion di HP, kolom biasa di md ke atas -->
                <div x-data="{ open: false }" class="border-t border-gray-100 md:border-0">
                    <button type="button" @click="open = !open" class="w-full flex items-center justify-between h-12 md:h-auto md:mb-4 text-left font-semibold text-gray-900 text-sm md:text-base md:cursor-default" :aria-expanded="open">
                        Navigasi
                        <svg class="w-4 h-4 text-gray-400 md:hidden transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" /></svg>
                    </button>
                    <ul class="pb-3 md:pb-0 space-y-2.5 md:!block" :class="open ? 'block' : 'hidden'">
                        <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-primary-600 text-[13px] transition-colors">Beranda</a></li>
                        <li><a href="{{ route('layanan.index') }}" class="text-gray-500 hover:text-primary-600 text-[13px] transition-colors">Ajukan Surat</a></li>
                        <li><a href="{{ route('layanan.surat.tracking') }}" class="text-gray-500 hover:text-primary-600 text-[13px] transition-colors">Cek Status</a></li>
                        <li><a href="{{ route('faq') }}" class="text-gray-500 hover:text-primary-600 text-[13px] transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <div x-data="{ open: false }" class="border-t border-gray-100 md:border-0">
                    <button type="button" @click="open = !open" class="w-full flex items-center justify-between h-12 md:h-auto md:mb-4 text-left font-semibold text-gray-900 text-sm md:text-base md:cursor-default" :aria-expanded="open">
                        Layanan
                        <svg class="w-4 h-4 text-gray-400 md:hidden transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" /></svg>
                    </button>
                    <ul class="pb-3 md:pb-0 space-y-2.5 md:!block" :class="open ? 'block' : 'hidden'">
                        <li><a href="{{ route('layanan.index') }}" class="text-gray-500 hover:text-primary-600 text-[13px] transition-colors">Daftar Layanan Surat</a></li>
                        <li><a href="{{ route('layanan.surat.template.index') }}" class="text-gray-500 hover:text-primary-600 text-[13px] transition-colors">Download Template</a></li>
                        <li><a href="{{ route('struktur-organisasi') }}" class="text-gray-500 hover:text-primary-600 text-[13px] transition-colors">Struktur Organisasi</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-100 py-4 text-center md:text-left">
                <p class="text-[11px] md:text-sm text-gray-400">© {{ date('Y') }} SiMPEL Landasan Ulin. All rights reserved.</p>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>

</html>
