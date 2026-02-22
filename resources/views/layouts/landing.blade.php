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

        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .transparent-nav {
            background: transparent;
            border-bottom: 1px solid transparent;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">

    <!-- Topbar -->
    <header
        class="fixed top-0 w-full z-50 transition-all duration-300 transform"
        x-data="{ mobileMenuOpen: false, scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 20)"
        :class="scrolled ? 'glass-nav shadow-sm py-2' : 'transparent-nav py-4'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img src="{{ asset('images/logo_simpel.png') }}" alt="SiMPEL" class="h-10 w-auto object-contain">
                    <div class="flex flex-col">
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-700">SiMPEL</span>
                        <span class="text-[10px] text-gray-500 font-medium tracking-wider uppercase">Landasan Ulin</span>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">Home</a>
                    <a href="{{ route('services.index') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">Buat Surat</a>
                    <a href="{{ route('tracking.index') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">Cek Status</a>
                    <a href="#faq" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">FAQ</a>
                    <a href="#contact" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">Contact Us</a>
                </nav>

                <!-- Auth Buttons -->
                {{--<div class="hidden lg:flex items-center gap-4">
                    @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-full hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transition-all transform hover:-translate-y-0.5">
                        Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-medium text-primary-600 bg-primary-50 rounded-full hover:bg-primary-100 transition-colors">
                        Masuk
                    </a>
                    @endauth
                </div>--}}

                <!-- Mobile Menu Button -->
                <div class="flex lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-gray-500 hover:text-gray-900 focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="lg:hidden bg-white border-t border-gray-100 shadow-lg absolute w-full z-40" style="display: none;">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="{{ url('/') }}" class="block px-3 py-3 text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg">Home</a>
                <a href="#buat-surat" class="block px-3 py-3 text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg">Buat Surat</a>
                <a href="#cek-status" class="block px-3 py-3 text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg">Cek Status</a>
                <a href="#faq" class="block px-3 py-3 text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg">FAQ</a>
                <a href="#contact" class="block px-3 py-3 text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg">Contact Us</a>

                <div class="border-t border-gray-100 my-2 pt-2">
                    @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-3 text-base font-medium text-center text-white bg-primary-600 rounded-lg hover:bg-primary-700">Dashboard</a>
                    @else
                    <div class="grid grid-cols-2 gap-3 px-3">
                        <a href="{{ route('login') }}" class="block px-3 py-2.5 text-base font-medium text-center text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100">Masuk</a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 lg:gap-8 mb-12">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/logo_simpel.png') }}" alt="SiMPEL" class="h-10 w-auto object-contain">
                        <span class="text-xl font-bold text-gray-900">SiMPEL</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        Sistem Informasi Pelayanan Administrasi Kecamatan Landasan Ulin. Memudahkan masyarakat dalam pengurusan surat menyurat secara digital.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-primary-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-primary-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.072 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Links 1 -->
                <div>
                    <h4 class="font-bold text-gray-900 mb-6">Navigasi</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Home</a></li>
                        <li><a href="#buat-surat" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Buat Surat</a></li>
                        <li><a href="#cek-status" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Cek Status</a></li>
                        <li><a href="#faq" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">FAQ</a></li>
                    </ul>
                </div>

                <!-- Links 2 -->
                <div>
                    <h4 class="font-bold text-gray-900 mb-6">Layanan</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">SKCK</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Surat Domisili</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Surat Kematian</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Surat Kelahiran</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div id="contact">
                    <h4 class="font-bold text-gray-900 mb-6">Hubungi Kami</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3 text-sm text-gray-500">
                            <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Jl. Ahmad Yani Km 24, Landasan Ulin, Banjarbaru
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-500">
                            <svg class="w-5 h-5 text-primary-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            kelurahan@landasanulin.id
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-400">© {{ date('Y') }} SiMPEL Landasan Ulin. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="text-sm text-gray-400 hover:text-primary-600 transition-colors">Privacy Policy</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-primary-600 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>