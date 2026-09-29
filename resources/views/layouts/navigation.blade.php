<nav x-data="{ open: false }" class="bg-white border-b border-slate-200/80 sticky top-0 z-20 shadow-xs">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 group">
                        <img
                            src="{{ asset('images/favicon.ico') }}"
                            alt="Logo"
                            class="h-9 w-9 object-contain"
                        />

                        <span class="text-lg font-extrabold tracking-tight text-slate-900">
                            CBT
                            <span style="color: #08CB00;">
                                MTsN 12 Jakarta
                            </span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown Desktop -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold text-slate-700 focus:outline-none transition shadow-2xs">
                            <!-- Initials Avatar -->
                            <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white text-xs font-extrabold flex items-center justify-center">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <span class="max-w-[140px] truncate text-xs sm:text-sm">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2.5 border-b border-slate-100">
                            <p class="text-xs text-slate-400">Masuk sebagai</p>
                            <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        @if(Auth::user()->isGuru())
                            <x-dropdown-link :href="route('guru.ujian.index')" class="text-xs font-semibold py-2">
                                {{ __('Daftar Ujian') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('guru.ujian.create')" class="text-xs font-semibold py-2">
                                {{ __('Buat Ujian Baru') }}
                            </x-dropdown-link>
                         @else
                            <a href="{{ route('siswa.dashboard') }}"
                                class="inline-flex items-center px-3.5 py-2 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('siswa.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Ujian Saya
                            </a>
                        @endif
                        

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 py-2"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Keluar (Log Out)') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Mobile Button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-md">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @if(Auth::user()->isGuru())
                <a href="{{ route('guru.ujian.index') }}"
                    class="block px-3 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('guru.ujian.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                     Daftar Ujian & Soal
                </a>
                <a href="{{ route('guru.ujian.create') }}"
                    class="block px-3 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('guru.ujian.create') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                     Buat Ujian Baru
                </a>
            @else
                <a href="{{ route('siswa.dashboard') }}"
                    class="block px-3 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('siswa.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    📝 Ujian Saya
                </a>
            @endif
        </div>

        <!-- Responsive User Profile Section -->
        <div class="pt-3 pb-3 border-t border-slate-100 px-4">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-xs">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="font-bold text-sm text-slate-800">{{ Auth::user()->name }}</div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ Auth::user()->isGuru() ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                        {{ Auth::user()->role }}
                    </span>
                </div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Pengaturan Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-50">
                        Keluar (Log Out)
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
