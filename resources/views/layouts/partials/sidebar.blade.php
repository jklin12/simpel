<aside class="fixed inset-y-0 left-0 bg-white shadow-xl max-h-screen w-64 flex flex-col z-30 border-r border-gray-100 font-sans transform transition-transform duration-300 lg:static lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-500/30">
                S
            </div>
            <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-800 to-gray-600">SiMPEL</span>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-4">
        <nav class="px-4 space-y-2">
            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('dashboard*') ? 'bg-blue-50 text-blue-600 shadow-sm' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-colors group-hover:text-blue-600 {{ request()->routeIs('dashboard*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Permohonan Group -->
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Permohonan Surat</p>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 transition-colors group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium">Semua Permohonan</span>
                    <span class="ml-auto bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs font-bold">12</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 transition-colors group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">Perlu Approval</span>
                    <span class="ml-auto bg-orange-100 text-orange-600 py-0.5 px-2 rounded-full text-xs font-bold">4</span>
                </a>
            </div>

            @role('super_admin')
            <!-- Master Data Group -->
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Master Data</p>

                <!-- Master Data Wilayah Group -->
                <div x-data="{ open: {{ request()->routeIs('admin.master.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" type="button" class="w-full flex items-center gap-3 px-4 py-3 text-gray-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group justify-between {{ request()->routeIs('admin.master.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 transition-colors group-hover:text-blue-600 {{ request()->routeIs('admin.master.*') ? 'text-blue-600' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="font-medium">Master Data Wilayah</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="px-4 py-2 space-y-1">
                        {{--<a href="{{ route('admin.master.kabupaten.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 {{ request()->routeIs('admin.master.kabupaten*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.master.kabupaten*') ? 'bg-blue-600' : 'bg-gray-400' }}"></span>
                        Kabupaten
                        </a>--}}
                        <a href="{{ route('admin.master.kecamatan.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 {{ request()->routeIs('admin.master.kecamatan*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.master.kecamatan*') ? 'bg-blue-600' : 'bg-gray-400' }}"></span>
                            Kecamatan
                        </a>
                        <a href="{{ route('admin.master.kelurahan.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 {{ request()->routeIs('admin.master.kelurahan*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.master.kelurahan*') ? 'bg-blue-600' : 'bg-gray-400' }}"></span>
                            Kelurahan
                        </a>
                    </div>
                </div>

                <!-- Access Control Group -->
                <div x-data="{ open: {{ request()->routeIs('admin.roles*', 'admin.permissions*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" type="button" class="w-full flex items-center gap-3 px-4 py-3 text-gray-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group justify-between {{ request()->routeIs('admin.roles*', 'admin.permissions*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 transition-colors group-hover:text-blue-600 {{ request()->routeIs('admin.roles*', 'admin.permissions*') ? 'text-blue-600' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="font-medium">Access Control</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="px-4 py-2 space-y-1">
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 {{ request()->routeIs('admin.roles*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.roles*') ? 'bg-blue-600' : 'bg-gray-400' }}"></span>
                            Roles
                        </a>
                        <a href="{{ route('admin.permissions.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-500 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 {{ request()->routeIs('admin.permissions*') ? 'text-blue-600 bg-blue-50 font-medium' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.permissions*') ? 'bg-blue-600' : 'bg-gray-400' }}"></span>
                            Permissions
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('admin.users*') ? 'bg-blue-50 text-blue-600 shadow-sm' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 transition-colors group-hover:text-blue-600 {{ request()->routeIs('admin.users*') ? 'text-blue-600' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="font-medium">User Management</span>
                </a>
            </div>
            @endrole

        </nav>
    </div>

    <!-- Bottom Action -->
    <div class="p-4 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-colors group-hover:text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium group-hover:font-semibold">Logout</span>
            </button>
        </form>
    </div>
</aside>