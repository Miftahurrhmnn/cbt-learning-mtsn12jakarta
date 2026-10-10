@props(['examCount' => null])

@php
    $guruUser = auth()->user();
    
    // Hitung total ujian jika tidak dipassing dari controller
    if ($examCount === null && $guruUser) {
        if ($guruUser->subjects()->exists()) {
            $subjectIds = $guruUser->subjects->pluck('id')->toArray();
            $examCount = \App\Models\Exam::where(function ($q) use ($guruUser, $subjectIds) {
                $q->where('user_id', $guruUser->id)
                  ->orWhereIn('subject_id', $subjectIds);
            })->count();
        } else {
            $examCount = \App\Models\Exam::where('user_id', $guruUser->id)->count();
        }
    }
    
    // Status aktif setiap menu
    $isCreateActive = request()->routeIs('guru.ujian.create');
    $isBankSoalActive = request()->routeIs('guru.bank-soal.*');
    $isMonitoringActive = request()->routeIs('guru.monitoring.*');
    $isCalendarActive = request()->routeIs('guru.fullcalendar.*');
    $isProfileActive = request()->routeIs('profile.*');
    $isDaftarUjianActive = !$isCreateActive && !$isBankSoalActive && !$isMonitoringActive && !$isProfileActive && !$isCalendarActive && (request()->routeIs('guru.ujian.*') || request()->routeIs('guru.dashboard'));

    // Inisial 2 Huruf Guru
    $nameParts = preg_split('/\s+/', trim($guruUser->name ?? 'Guru'));
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
                    <h2 class="font-black text-slate-900 text-sm leading-tight">CBT MTsN 12 Jakarta</h2>
                    <p class="text-[11px] font-medium text-slate-400 mt-0.5">Portal Guru & Ujian</p>
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
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Manajemen Ujian</p>
            
            <!-- Daftar Ujian -->
            <a href="{{ route('guru.ujian.index') }}" 
               class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isDaftarUjianActive ? 'bg-indigo-50 text-indigo-600 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 font-semibold' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ $isDaftarUjianActive ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Daftar Ujian</span>
                </div>
                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $isDaftarUjianActive ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $examCount ?? 0 }}
                </span>
            </a>

            <!-- Buat Ujian Baru -->
            <a href="{{ route('guru.ujian.create') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isCreateActive ? 'bg-indigo-50 text-indigo-600 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 font-semibold' }}">
                <svg class="w-5 h-5 {{ $isCreateActive ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Buat Ujian Baru</span>
            </a>

            <!-- Bank Soal -->
            <a href="{{ route('guru.bank-soal.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isBankSoalActive ? 'bg-indigo-50 text-indigo-600 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 font-semibold' }}">
                <svg class="w-5 h-5 {{ $isBankSoalActive ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Bank Soal</span>
            </a>

            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 pt-3">Monitoring & Evaluasi</p>

            <!-- Monitoring Siswa -->
            <a href="{{ route('guru.monitoring.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isMonitoringActive ? 'bg-indigo-50 text-indigo-600 shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 font-semibold' }}">
                <svg class="w-5 h-5 {{ $isMonitoringActive ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Monitoring Siswa</span>
            </a>
        </nav>
    </div>

    <!-- Mobile User Profile & Logout -->
    <div class="p-4 border-t border-slate-100 bg-white">
        <div class="flex items-center justify-between gap-3 px-2">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white font-extrabold text-sm shadow-xs">
                    {{ $initials }}
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-white truncate leading-tight">{{ $guruUser->name }}</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-500 mt-1">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Guru Aktif
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar (Log Out)" class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                    <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ==================== DESKTOP FIXED/STICKY SIDEBAR ==================== -->
<aside class="hidden md:flex md:w-72 md:flex-col md:shrink-0 h-screen sticky top-0 bg-[#1B2430] text-white z-30 justify-between">
    <div class="p-6">
        <!-- Brand Header Desktop -->
        <div class="flex items-center gap-3 pb-6 border-b border-slate-100">
            <img src="{{ asset('images/favicon.ico') }}" alt="Logo" class="w-10 h-10 object-contain shrink-0">
            <div>
                <h2 class="font-black text-whitetext-base leading-tight">CBT MTsN 12 Jakarta</h2>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Portal Guru & Ujian</p>
            </div>
        </div>

        <!-- Navigation Section Desktop -->
        <nav class="mt-6 space-y-1.5">
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5">Manajemen Ujian</p>
            
            <!-- Daftar Ujian -->
            <a href="{{ route('guru.ujian.index') }}" 
               class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isDaftarUjianActive ? 'bg-blue-500 text-white shadow-2xs' : 'text-white hover:bg-blue-500 hover:text-white font-semibold' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ $isDaftarUjianActive ? 'text-white' : 'text-slate-50' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-white">Daftar Ujian</span>
                </div>
                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $isDaftarUjianActive ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $examCount ?? 0 }}
                </span>
            </a>

            <!-- Buat Ujian Baru -->
            <a href="{{ route('guru.ujian.create') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isCreateActive ? 'bg-blue-500 text-white shadow-2xs' : 'text-white hover:bg-blue-500 hover:text-white font-semibold' }}">
                <svg class="w-5 h-5 {{ $isCreateActive ? 'text-white' : 'text-slate-50' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Buat Ujian Baru</span>
            </a>

            <!-- Bank Soal -->
            <a href="{{ route('guru.bank-soal.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isBankSoalActive ? 'bg-blue-500 text-white shadow-2xs' : 'text-white hover:bg-blue-500 hover:text-white font-semibold' }}">
                <svg class="w-5 h-5 {{ $isBankSoalActive ? 'text-white' : 'text-slate-50' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Bank Soal</span>
            </a>

            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 pt-4">Monitoring & Evaluasi</p>

            <!-- Monitoring Siswa -->
            <a href="{{ route('guru.monitoring.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isMonitoringActive ? 'bg-blue-500 text-white shadow-2xs' : 'text-white hover:bg-blue-500 hover:text-white font-semibold' }}">
                <svg class="w-5 h-5 {{ $isMonitoringActive ? 'text-white' : 'text-slate-50' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Monitoring Siswa</span>
            </a>

            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 pt-4">Penjadwalan</p>

            <!-- Calendar -->
            <a href="{{ route('guru.fullcalendar.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-bold transition-all duration-150 {{ $isCalendarActive ? 'bg-blue-500 text-white shadow-2xs' : 'text-white hover:bg-blue-500 hover:text-white font-semibold' }}">
                <!-- Icon Kalender -->
                <svg class="w-5 h-5 {{ $isCalendarActive ? 'text-white' : 'text-slate-50' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Agenda</span>
            </a>
        </nav>
    </div>

    <!-- Desktop User Profile & Logout -->
    <div class="p-4 border-t border-slate-100 bg-[#1B2430]">
        <div class="flex items-center justify-between gap-3 px-2">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white font-extrabold text-sm shadow-xs">
                    {{ $initials }}
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-white truncate leading-tight">{{ $guruUser->name }}</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-500 mt-1">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Guru Aktif
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar (Log Out)" class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                    <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
