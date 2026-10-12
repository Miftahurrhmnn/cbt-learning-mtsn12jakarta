<x-app-layout :hide-nav="Auth::user()->isSiswa()">
    @if(Auth::user()->isSiswa())
        <!-- ==================== SISWA PROFILE TOP HEADER ==================== -->
        <div class="min-h-screen bg-slate-100/70 font-sans text-slate-800 antialiased pb-28">
            <header class="bg-[#2B77DE] bg-gradient-to-b from-[#266210] to-[#063B00] text-white pt-5 pb-16 px-4 sm:px-6 lg:px-8 rounded-b-[2.5rem] shadow-md relative overflow-hidden">
                <!-- Background Decorative Circles -->
                <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
                <div class="absolute top-1/2 -left-10 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>

                <div class="max-w-xl mx-auto relative z-10 flex items-center justify-between">
                    <!-- Back Button & Title -->
                    <div class="flex items-center gap-3">
                        <div>
                            <h1 class="text-lg sm:text-xl font-black text-white leading-tight">
                                Profil Siswa
                            </h1>
                            <p class="text-xs text-white/80 font-medium mt-0.5">
                                Informasi Identitas & Akun CBT
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="max-w-xl mx-auto px-4 sm:px-6 -mt-10 relative z-20 space-y-5">
                <!-- ==================== SISWA IDENTITY CARD ==================== -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-100 p-6">
                    <div class="flex flex-col items-center text-center">
                        <!-- Avatar Initial -->
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#266210] to-[#063B00] text-white flex items-center justify-center text-2xl font-black shadow-lg shadow-emerald-950/20 ring-4 ring-white">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>

                        <!-- Name & Active Status -->
                        <div class="mt-3">
                            <h2 class="text-lg font-black text-slate-800 leading-tight">{{ $user->name }}</h2>
                            <div class="mt-1 flex items-center justify-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Siswa Aktif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Data Siswa: Nama, NISN, Kelas -->
                    <div class="mt-6 pt-5 border-t border-slate-100 space-y-3">
                        <!-- 1. Nama Lengkap -->
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-100/70 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-800 block">{{ $user->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. NISN -->
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100/70 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NISN / Nomor Induk</span>
                                    <span class="text-xs sm:text-sm font-black font-mono text-slate-800 block">{{ $user->nisn ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Kelas -->
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-amber-100/70 text-amber-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kelas</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-800 block">{{ $user->classroom->name ?? 'Belum Ditentukan' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Lembaga / Sekolah -->
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100/80">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-purple-100/70 text-purple-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Madrasah / Sekolah</span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-800 block">MTsN 12 Jakarta</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== NOTICE BANNER ==================== -->
                <div class="rounded-2xl bg-amber-50/80 border border-amber-200/70 p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-xs text-amber-800 leading-relaxed">
                        <p class="font-bold">Informasi Akun Siswa</p>
                        <p class="mt-0.5 text-amber-700/90">
                            Data profil dan kata sandi siswa dikelola secara terpusat oleh proktor / pihak madrasah. Jika terdapat kekeliruan data (Nama, NISN, atau Kelas), silakan melapor ke panitia ujian CBT.
                        </p>
                    </div>
                </div>

                <!-- ==================== LOGOUT BUTTON CARD ==================== -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin keluar dari akun CBT?')"
                                class="w-full flex items-center justify-center gap-2 px-5 py-3.5 bg-rose-600 hover:bg-rose-700 active:scale-[0.99] text-white font-bold rounded-2xl shadow-lg shadow-rose-600/20 transition cursor-pointer text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar dari Akun (Logout)</span>
                        </button>
                    </form>
                </div>
            </main>

            <!-- ==================== FLOATING BOTTOM NAVIGATION DOCK ==================== -->
            <nav class="fixed bottom-4 inset-x-0 z-50 px-4 pointer-events-none">
                <div class="max-w-xs mx-auto bg-white/95 backdrop-blur-xl border border-slate-200/90 shadow-2xl shadow-slate-900/15 rounded-full p-1.5 flex items-center justify-around pointer-events-auto transition-all duration-300">
                    
                    <!-- 1. Beranda -->
                    <a href="{{ route('siswa.dashboard') }}" 
                       class="relative flex items-center justify-center gap-2 px-5 py-2.5 rounded-full transition-all duration-300 font-bold text-xs select-none text-slate-500 hover:text-slate-900 hover:bg-slate-100/80">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Beranda</span>
                    </a>

                    <!-- 2. Profil Siswa (Active) -->
                    <a href="{{ route('profile.edit') }}" 
                       class="relative flex items-center justify-center gap-2 px-5 py-2.5 rounded-full transition-all duration-300 font-bold text-xs select-none bg-blue-600 text-white shadow-md shadow-blue-600/30">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Profil</span>
                    </a>

                </div>
            </nav>
        </div>

    @else
        <!-- ==================== ADMIN & GURU STANDARD PROFILE PAGE ==================== -->
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                @if($user->isGuru())
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-teacher-subjects-form')
                        </div>
                    </div>
                @endif

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
