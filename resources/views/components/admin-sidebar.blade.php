@props(['studentCount' => null])

@php
    $adminUser = auth()->user();
    
    // Hitung total siswa jika belum dipassing
    if ($studentCount === null) {
        $studentCount = \App\Models\User::where('role', 'siswa')->count();
    }
    
    // Status aktif setiap menu
    $isDashboardActive = request()->routeIs('admin.dashboard');
    $isCreateSiswaActive = request()->routeIs('admin.siswa.create');
    $isSiswaIndexActive = request()->routeIs('admin.siswa.index') || (request()->routeIs('admin.siswa.*') && !$isCreateSiswaActive);
    $isProfileActive = request()->routeIs('profile.*');

    // Inisial 2 Huruf Admin
    $nameParts = preg_split('/\s+/', trim($adminUser->name ?? 'Admin'));
    $initials = count($nameParts) >= 2 
        ? strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1))
        : strtoupper(mb_substr($nameParts[0], 0, 2));
@endphp

<!-- ==================== MOBILE BACKDROP OVERLAY ==================== -->
<div 
    x-show="sidebarOpen" 
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false" 
    class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs md:hidden"
    style="display: none;">
</div>

<!-- ==================== MOBILE OFF-CANVAS SIDEBAR ==================== -->
<aside 
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    @keydown.escape.window="sidebarOpen = false"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white flex flex-col justify-between shadow-2xl md:hidden overflow-y-auto"
    style="display: none;">
    
    <div class="p-6">
        <!-- Mobile Header with Close Button -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/favicon.ico') }}" alt="Logo" class="w-9 h-9 object-contain shrink-0">
                <div>
                    <h2 class="font-black text-white text-sm leading-tight">CBT MTsN 12 Jakarta</h2>
                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md mt-1">
                        <svg class="w-3 h-3 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Portal Administrator
                    </span>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" aria-label="Tutup Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <nav class="mt-6 space-y-1.5">
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Menu Utama</p>
            
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isDashboardActive ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-semibold' }}">
                <svg class="w-5 h-5 {{ $isDashboardActive ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Data Siswa -->
            <a href="{{ route('admin.siswa.index') }}" 
               class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isSiswaIndexActive ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-semibold' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ $isSiswaIndexActive ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Data Siswa</span>
                </div>
                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $isSiswaIndexActive ? 'bg-slate-700 text-slate-100' : 'bg-slate-100 text-slate-600' }}">
                    {{ $studentCount }}
                </span>
            </a>

            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 pt-3">Pengaturan</p>

            <!-- Profil Saya -->
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isProfileActive ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-semibold' }}">
                <svg class="w-5 h-5 {{ $isProfileActive ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profil Saya</span>
            </a>
        </nav>
    </div>

    <!-- Mobile User Profile & Logout -->
    <div class="p-4 border-t border-slate-100 bg-white">
        <div class="flex items-center justify-between gap-3 px-2">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white font-extrabold text-sm shadow-xs">
                    {{ $initials }}
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-slate-900 truncate leading-tight">{{ $adminUser->name }}</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-rose-600 mt-1">
                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                        Admin CBT
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar (Log Out)" class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer">
                    <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ==================== DESKTOP FIXED/STICKY SIDEBAR ==================== -->
<aside class="hidden md:flex md:w-72 md:flex-col md:shrink-0 h-screen sticky top-0 bg-black text-white border-r border-slate-200/80 z-30 justify-between shadow-xs">
    <div class="p-6">
        <!-- Brand Header Desktop -->
        <div class="flex items-center gap-3 pb-6 border-b border-slate-100">
           <img src="{{ asset('images/favicon.ico') }}" alt="Logo" class="w-10 h-10 object-contain shrink-0">
            <div>
                <h2 class="font-black text-white text-base leading-tight">CBT MTsN 12 Jakarta</h2>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">
                        <svg class="w-3 h-3 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Administrator
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Section Desktop -->
        <nav class="mt-6 space-y-1.5">
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5">Menu Utama</p>
            
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isDashboardActive ? 'bg-blue-500 text-white shadow-xs' : 'text-white hover:bg-blue-500 hover:text-white font-semibold' }}">
                <svg class="w-5 h-5 {{ $isDashboardActive ? 'text-white' : 'text-slate-50' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Data Siswa -->
            <a href="{{ route('admin.siswa.index') }}" 
               class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isSiswaIndexActive ? 'bg-blue-500 text-white shadow-xs' : 'text-white hover:bg-blue-500 hover:text-white font-semibold' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ $isSiswaIndexActive ? 'text-white' : 'text-slate-50' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Data Siswa</span>
                </div>
                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $isSiswaIndexActive ? 'bg-slate-700 text-slate-100' : 'bg-slate-100 text-slate-600' }}">
                    {{ $studentCount }}
                </span>
            </a>

            <!-- Tambah Siswa Baru (MENU BARU) -->
            <a href="{{ route('admin.siswa.create') }}" 
            class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ request()->routeIs('admin.siswa.create') ? 'bg-blue-500 text-white shadow-xs' : 'text-white hover:bg-blue-500 hover:text-white font-semibold' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.siswa.create') ? 'text-white' : 'text-slate-50' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Tambah Siswa</span>
            </a>
        </nav>
    </div>

    <!-- Desktop User Profile & Logout -->
    <div class="p-4 border-t border-slate-100 bg-black">
        <div class="flex items-center justify-between gap-3 px-2">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white font-extrabold text-sm shadow-xs">
                    {{ $initials }}
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-white truncate leading-tight">{{ $adminUser->name }}</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-rose-600 mt-0.5">
                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                        Admin CBT
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar (Log Out)" class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer">
                    <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
