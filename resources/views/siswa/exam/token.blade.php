<x-app-layout :hide-nav="true">
    <div x-data="{ profileModalOpen: false }" 
         class="min-h-screen bg-slate-100/70 font-sans text-slate-800 antialiased pb-28 md:pb-16 flex flex-col justify-between selection:bg-blue-500 selection:text-white">

        <!-- ==================== TOP BLUE APP HEADER (Selaras dengan Dashboard Siswa) ==================== -->
        <header class="bg-[#2B77DE] bg-gradient-to-b from-[#2B77DE] to-[#1F67CB] text-white pt-4 pb-16 px-4 sm:px-6 lg:px-8 rounded-b-[2.5rem] shadow-md relative overflow-hidden">
            <!-- Background Decorative Circles -->
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute top-1/2 -left-10 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>

            <div class="max-w-4xl mx-auto relative z-10 space-y-3">
                <!-- Profile Snippet Header -->
                <div class="flex items-center gap-4 pt-1 pb-1">
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white/20 border-2 border-white/60 flex items-center justify-center text-white overflow-hidden shadow-inner">
                            <svg class="w-9 h-9 sm:w-10 sm:h-10 text-white/90 translate-y-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-blue-100 flex items-center gap-1.5 mb-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> CBT MTsN 12 Jakarta
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-white truncate leading-tight">
                            {{ Auth::user()->name }}
                        </h2>
                        <p class="text-xs font-mono text-white/80 mt-0.5 truncate tracking-wide">
                            {{ Auth::user()->nisn ?? 'Siswa MTsN 12' }}
                        </p>
                        <p class="text-[11px] font-medium text-white/75 truncate mt-0.5">
                            {{ Auth::user()->classroom->name ?? 'Kelas Siswa' }} &bull; MTsN 12 Jakarta
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- ==================== MAIN FLOATING TOKEN CARD ==================== -->
        <main class="max-w-lg w-full mx-auto px-4 sm:px-6 -mt-10 relative z-20 flex-1 flex flex-col justify-center">
            
            <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-xl shadow-slate-200/70">

                <!-- Card Header -->
                <div class="border-b border-slate-100 bg-slate-50/60 px-6 py-6 text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-[#2B77DE] shadow-xs">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Masukkan Token Ujian
                    </h1>

                    <p class="mt-1.5 text-xs sm:text-sm leading-relaxed text-slate-500 max-w-sm mx-auto">
                        Masukkan token yang diberikan oleh guru pengawas untuk memulai ujian.
                    </p>
                </div>

                <!-- Card Content -->
                <div class="p-6 sm:p-7 space-y-6">

                    <!-- Ringkasan Paket Ujian (Selaras dengan Card Ujian Dashboard) -->
                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700">
                                {{ $exam->subject->name ?? 'Mata Pelajaran' }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                @if($exam->day_of_week)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                        📅 {{ $exam->day_of_week }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700">
                                    Kelas: {{ $exam->all_classroom_names }}
                                </span>
                            </div>
                        </div>

                        <h4 class="font-bold text-sm sm:text-base text-slate-900 leading-snug">
                            {{ $exam->title ?? 'Ujian ' . ($exam->subject->name ?? '') }}
                        </h4>

                        <!-- Konfirmasi Hak Akses Kelas Siswa -->
                        <div class="p-2.5 bg-emerald-50/90 border border-emerald-200/80 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold">✓</span>
                                <span>Akses Ujian Khusus: <strong>{{ $exam->all_classroom_names }}</strong></span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-md">Kelas Anda Sesuai</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-500 pt-2 border-t border-slate-200/60">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="truncate">Jam: <strong class="text-slate-800">{{ $exam->formatted_time_range }}</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Jumlah: <strong class="text-slate-800">{{ $exam->questions_count ?? $exam->questions->count() }}</strong> Soal</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Token -->
                    <form method="POST" action="{{ route('siswa.ujian.verify-token', $exam->id) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="token" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Token Ujian <span class="text-rose-500">*</span>
                            </label>

                            <input
                                id="token"
                                name="token"
                                type="text"
                                value="{{ old('token') }}"
                                minlength="7"
                                maxlength="7"
                                required
                                autocomplete="off"
                                autofocus
                                placeholder="CONTOH: MTK2026"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 7)"
                                class="block w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 text-slate-900 font-mono font-black text-xl sm:text-2xl text-center uppercase tracking-[0.35em] placeholder:tracking-normal placeholder:font-sans placeholder:font-normal placeholder:text-slate-300 focus:border-[#2B77DE] focus:ring-4 focus:ring-blue-500/15 transition shadow-2xs bg-white"
                            >
                            @error('token')
                                <div class="mt-2.5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs font-bold text-rose-700 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Tombol Submit (Sesuai Desain Tombol Biru di Foto Dashboard) -->
                        <button
                            type="submit"
                            class="w-full mt-6 py-3.5 px-6 rounded-2xl bg-[#2B77DE] hover:bg-[#1F67CB] text-white font-bold text-xs sm:text-sm uppercase tracking-wider shadow-md shadow-blue-500/25 flex items-center justify-center gap-2.5 transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#2B77DE] focus:ring-offset-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <span>Mulai Ujian (Token)</span>
                        </button>
                    </form>

                    <!-- Navigasi Balik -->
                    <div class="text-center pt-2">
                        <a href="{{ route('siswa.dashboard') }}" 
                           class="text-xs sm:text-sm font-semibold text-slate-500 hover:text-[#2B77DE] transition inline-flex items-center gap-1">
                            Kembali ke Dashboard
                        </a>
                    </div>

                </div>
            </div>

        </main>

        <!-- ==================== MODAL PROFIL SISWA ==================== -->
        <div x-show="profileModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click="profileModalOpen = false">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative text-center space-y-4" @click.stop>
                <div class="w-16 h-16 rounded-full bg-[#2B77DE] text-white flex items-center justify-center mx-auto text-xl font-black shadow-md">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>

                <div>
                    <h3 class="font-extrabold text-base text-slate-900">{{ Auth::user()->name }}</h3>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ Auth::user()->email }}</p>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 text-left text-xs space-y-2 border border-slate-100">
                    <div class="flex justify-between">
                        <span class="text-slate-400">NISN / No. Induk:</span>
                        <strong class="text-slate-700 font-mono">{{ Auth::user()->nisn ?? '-' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kelas:</span>
                        <strong class="text-blue-700">{{ Auth::user()->classroom->name ?? 'Belum Ditentukan' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Sekolah:</span>
                        <strong class="text-slate-700">MTsN 12 Jakarta</strong>
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <form method="POST" action="{{ route('logout') }}" class="w-full inline">
                        @csrf
                        <button type="submit" class="w-full py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold transition">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ==================== FLOATING BOTTOM APP NAVIGATION DOCK (Selaras dengan Dashboard) ==================== -->
        <nav class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-2 shadow-2xl">
            <div class="max-w-md mx-auto flex items-center justify-around relative">
                <!-- 1. Home Icon -->
                <a href="{{ route('siswa.dashboard') }}" 
                   class="flex flex-col items-center justify-center p-2 text-[#2B77DE] hover:opacity-80 transition"
                   title="Beranda">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>

                <!-- 2. User Profile Icon -->
                <button type="button" @click="profileModalOpen = true" 
                        class="flex flex-col items-center justify-center p-2 text-slate-500 hover:text-[#2B77DE] transition"
                        title="Profil Siswa">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </button>
            </div>
        </nav>

    </div>
</x-app-layout>