<x-app-layout :hide-nav="true">
    <div class="min-h-screen bg-slate-100/70 font-sans text-slate-800 antialiased pb-12 flex flex-col justify-between selection:bg-blue-500 selection:text-white">

        <!-- ==================== TOP BLUE APP HEADER (Selaras dengan Dashboard Siswa) ==================== -->
        <header class="bg-[#2B77DE] bg-gradient-to-b from-[#266210] to-[#063B00] text-white pt-4 pb-16 px-4 sm:px-6 lg:px-8 rounded-b-[2.5rem] shadow-md relative overflow-hidden">
            <!-- Background Decorative Circles -->
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute top-1/2 -left-10 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>

            <div class="max-w-4xl mx-auto relative z-10 space-y-3">
                <!-- Profile Snippet Header -->
                <div class="flex items-center justify-between gap-4 pt-1 pb-1">
                    <div class="flex items-center gap-3.5 sm:gap-4 min-w-0">
                        <div class="relative shrink-0">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white/20 border-2 border-white/60 flex items-center justify-center text-white overflow-hidden shadow-inner font-extrabold text-lg sm:text-xl">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h2 class="text-base sm:text-lg font-bold text-white truncate leading-tight">
                                {{ Auth::user()->name }}
                            </h2>
                            <p class="text-xs font-mono text-white/80 mt-0.5 truncate tracking-wide">
                                {{ Auth::user()->nisn ?? 'Siswa MTsN 12' }}
                            </p>
                            <p class="text-[11px] font-medium text-white/75 truncate mt-0.5">
                                {{ Auth::user()->classroom->name ?? 'Kelas Siswa' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-xs border border-white/20 text-xs font-bold text-white tracking-wide">
                        <span>CBT MTsN 12</span>
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

                    <!-- Feedback Alert Session -->
                    @if(session('error'))
                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 text-xs font-semibold shadow-2xs">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($exam->hasNotStartedYet())
                        <!-- Alert Peringatan: Jam Belum Dimulai -->
                        <div class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-900 shadow-2xs flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs sm:text-sm font-black text-amber-950 uppercase tracking-wide">Peringatan: Ujian Belum Dimulai!</h4>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                    Ujian ini dijadwalkan pada jam <strong>{{ $exam->formatted_time_range }}</strong>. Anda belum dapat memasukkan token dan memulai ujian sebelum waktu menunjukkan pukul <strong>{{ substr($exam->start_time, 0, 5) }} WIB</strong>.
                                </p>
                                <div class="mt-3">
                                    <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>Refresh / Cek Jam Sekarang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($exam->hasEnded())
                        <!-- Alert Peringatan: Jam Sudah Berakhir -->
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-2xs flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs sm:text-sm font-black text-rose-950 uppercase tracking-wide">Waktu Ujian Telah Berakhir</h4>
                                <p class="text-xs text-rose-800 mt-1 leading-relaxed">
                                    Jadwal ujian ini telah berakhir (batas selesai pukul {{ substr($exam->end_time, 0, 5) }} WIB). Anda tidak dapat mengakses atau memulai ujian ini lagi.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Ringkasan Paket Ujian (Selaras dengan Card Ujian Dashboard) -->
                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700">
                                {{ $exam->subject->name ?? 'Mata Pelajaran' }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                @if($exam->day_of_week)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                        <svg class="w-3 h-3 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $exam->day_of_week }}
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
                                <span class="w-5 h-5 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-[10px]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
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

                            @if($exam->hasNotStartedYet())
                                <input
                                    id="token"
                                    name="token"
                                    type="text"
                                    disabled
                                    placeholder="MENUNGGU PUKUL {{ substr($exam->start_time, 0, 5) }} WIB"
                                    class="block w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 text-slate-400 font-mono font-bold text-base text-center uppercase tracking-wider bg-slate-100 cursor-not-allowed shadow-2xs"
                                >
                            @elseif($exam->hasEnded())
                                <input
                                    id="token"
                                    name="token"
                                    type="text"
                                    disabled
                                    placeholder="WAKTU UJIAN TELAH BERAKHIR"
                                    class="block w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 text-slate-400 font-mono font-bold text-base text-center uppercase tracking-wider bg-slate-100 cursor-not-allowed shadow-2xs"
                                >
                            @else
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
                            @endif

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
                        @if($exam->hasNotStartedYet())
                            <button
                                type="button"
                                disabled
                                class="w-full mt-6 py-3.5 px-6 rounded-2xl bg-amber-500/80 text-white font-bold text-xs sm:text-sm uppercase tracking-wider shadow-md flex items-center justify-center gap-2 cursor-not-allowed"
                            >
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Ujian Belum Dimulai (Mulai {{ substr($exam->start_time, 0, 5) }} WIB)</span>
                            </button>
                        @elseif($exam->hasEnded())
                            <button
                                type="button"
                                disabled
                                class="w-full mt-6 py-3.5 px-6 rounded-2xl bg-slate-300 text-slate-500 font-bold text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed"
                            >
                                <span>Waktu Ujian Berakhir</span>
                            </button>
                        @else
                            <button
                                type="submit"
                                class="w-full mt-6 py-3.5 px-6 rounded-2xl bg-[#2B77DE] hover:bg-[#1F67CB] text-white font-bold text-xs sm:text-sm uppercase tracking-wider shadow-md shadow-blue-500/25 flex items-center justify-center gap-2.5 transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#2B77DE] focus:ring-offset-2 cursor-pointer"
                            >
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                <span>Mulai Ujian</span>
                            </button>
                        @endif
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

    </div>
</x-app-layout>