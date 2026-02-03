<header class="bg-white/80 backdrop-blur-md sticky top-0 z-20 border-b border-gray-50/50 shadow-sm" x-data="{ userMenuOpen: false }">
    <div class="flex items-center justify-between h-16 px-6 lg:px-8">
        <!-- Mobile Menu Button & Title -->
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h2 class="text-xl font-semibold text-gray-800 hidden sm:block">
                @yield('title', 'Dashboard')
            </h2>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-6">
            <!-- Notifications -->
            <div class="relative" x-data="{ notificationOpen: false }">
                <button @click="notificationOpen = !notificationOpen" @click.outside="notificationOpen = false" class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                    @if(Auth::user()->unreadNotifications->count() > 0)
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white animate-pulse"></span>
                    @endif
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                <!-- Notification Dropdown -->
                <div x-show="notificationOpen"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 origin-top-right overflow-hidden"
                    style="display: none;">

                    <div class="px-4 py-2 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 text-sm">Notifications</h3>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full font-medium">{{ Auth::user()->unreadNotifications->count() }} New</span>
                        @endif
                    </div>

                    <div class="max-h-64 overflow-y-auto">
                        @forelse(Auth::user()->unreadNotifications as $notification)
                        <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 mt-1">
                                    @if($notification->data['type'] == 'success')
                                    <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5"></div>
                                    @elseif($notification->data['type'] == 'warning')
                                    <div class="w-2 h-2 rounded-full bg-orange-500 mt-1.5"></div>
                                    @else
                                    <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($notification->data['message'] ?? '', 60) }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="px-4 py-6 text-center text-gray-400">
                            <p class="text-xs">Tidak ada notifikasi baru</p>
                        </div>
                        @endforelse
                    </div>

                    @if(Auth::user()->unreadNotifications->count() > 0)
                    <div class="border-t border-gray-50 bg-gray-50/50 p-2 text-center">
                        <form action="{{ route('notifications.markAllRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-700 font-medium w-full">Tandai semua dibaca</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative pl-6 border-l border-gray-100">
                <button @click="userMenuOpen = !userMenuOpen" @click.outside="userMenuOpen = false" class="flex items-center gap-3 focus:outline-none group">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-semibold text-gray-700 group-hover:text-blue-600 transition-colors">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-blue-600 font-medium">
                            {{ strtoupper(str_replace('_', ' ', Auth::user()->getRoleNames()->first())) }}
                        </p>
                    </div>

                    @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover shadow-md ring-2 ring-white">
                    @else
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 p-[2px] shadow-md ring-2 ring-white group-hover:ring-blue-100 transition-all">
                        <div class="w-full h-full rounded-full bg-white flex items-center justify-center overflow-hidden">
                            <span class="font-bold text-gray-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                    @endif

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': userMenuOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="userMenuOpen"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50 origin-top-right"
                    style="display: none;">

                    <div class="px-4 py-3 border-b border-gray-50 md:hidden">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    @unlessrole('super_admin')
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Edit Profile
                    </a>

                    <div class="border-t border-gray-50 my-1"></div>
                    @endunlessrole

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>