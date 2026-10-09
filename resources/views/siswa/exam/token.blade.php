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
                            <!-- Circular Avatar Silhouette -->
                            <div class="relative shrink-0">
                                <div class="w-16 h-16 rounded-full bg-white/20 border-2 border-white/60 flex items-center justify-center text-white overflow-hidden shadow-inner">
                                    <svg class="w-10 h-10 text-white/90 translate-y-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
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
                </div>
            </div>
        </header>

        <!-- ==================== MAIN FLOATING TOKEN CARD ==================== -->
        <main class="max-w-lg w-full mx-auto px-4 sm:px-6 my-auto py-8 relative z-20 flex-1 flex flex-col justify-center">
            
            <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-2xl shadow-slate-200/60 transition-all">

                <!-- Card Header: Clean Visual Hierarchy -->
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-6 sm:px-8 text-center">

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Masukkan Token Ujian
                    </h1>

                    <p class="mt-1.5 text-xs sm:text-sm leading-relaxed text-slate-500 max-w-xs mx-auto">
                        Minta token ujian kepada pengawas ruangan untuk membuka akses soal.
                    </p>
                </div>

                <!-- Content Body: Padding konsisten -->
                <div class="p-6 sm:p-8 space-y-6">

                    <!-- Feedback Alert Session -->
                    @if(session('error'))
                        <div role="alert" class="p-4 bg-rose-50 border border-rose-200/80 rounded-2xl flex items-start gap-3 text-rose-900 text-xs sm:text-sm">
                            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="font-medium leading-snug">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($exam->hasNotStartedYet())
                        <!-- Alert Peringatan: Jam Belum Dimulai -->
                        <div role="status" class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-900 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-xs sm:text-sm font-bold text-amber-950 uppercase tracking-wide">Ujian Belum Dimulai</h2>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                    Pengerjaan dibuka pukul <strong>{{ substr($exam->start_time, 0, 5) }} WIB</strong>. Silakan periksa jam berkala.
                                </p>
                                <div class="mt-3">
                                    <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-95 text-white text-xs font-semibold transition shadow-sm cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span>Cek Waktu Sekarang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($exam->hasEnded())
                        <!-- Alert Peringatan: Jam Sudah Berakhir -->
                        <div role="status" class="p-4 rounded-2xl bg-rose-50/80 border border-rose-200 text-rose-900 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-xs sm:text-sm font-bold text-rose-950 uppercase tracking-wide">Waktu Ujian Telah Selesai</h2>
                                <p class="text-xs text-rose-800 mt-1 leading-relaxed">
                                    Batas pengerjaan ujian telah berakhir pada pukul {{ substr($exam->end_time, 0, 5) }} WIB.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Ringkasan Informasi Ujian -->
                    <div class="rounded-2xl border border-slate-200/70 bg-slate-50/50 p-4 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">
                                {{ $exam->subject->name ?? 'Mata Pelajaran' }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                @if($exam->day_of_week)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-200/60 text-slate-700">
                                        <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $exam->day_of_week }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-200/60 text-slate-700">
                                    {{ $exam->all_classroom_names }}
                                </span>
                            </div>
                        </div>

                        <h3 class="font-bold text-base text-slate-900 leading-snug">
                            {{ $exam->title ?? 'Ujian ' . ($exam->subject->name ?? '') }}
                        </h3>

                        <!-- Status Validasi Kelas -->
                        <div class="p-2.5 bg-emerald-50 border border-emerald-200/60 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                                <span class="font-medium">Sesuai dengan Kelas Anda</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md">
                                Terverifikasi
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-500 pt-2 border-t border-slate-200/60">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="truncate">Waktu: <strong class="text-slate-800 font-semibold">{{ $exam->formatted_time_range }}</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Jumlah: <strong class="text-slate-800 font-semibold">{{ $exam->questions_count ?? $exam->questions->count() }}</strong> Soal</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Input Token -->
                    <form method="POST" action="{{ route('siswa.ujian.verify-token', $exam->id) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="token" class="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Token Ujian <span class="text-rose-500" aria-hidden="true">*</span>
                            </label>

                            @if($exam->hasNotStartedYet())
                                <input
                                    id="token"
                                    name="token"
                                    type="text"
                                    disabled
                                    placeholder="UJIAN BELUM DIMULAI"
                                    class="block w-full px-4 py-3.5 rounded-2xl border border-slate-200 text-slate-400 font-mono font-bold text-sm text-center uppercase tracking-wider bg-slate-100 cursor-not-allowed select-none"
                                >
                            @elseif($exam->hasEnded())
                                <input
                                    id="token"
                                    name="token"
                                    type="text"
                                    disabled
                                    placeholder="UJIAN TELAH BERAKHIR"
                                    class="block w-full px-4 py-3.5 rounded-2xl border border-slate-200 text-slate-400 font-mono font-bold text-sm text-center uppercase tracking-wider bg-slate-100 cursor-not-allowed select-none"
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
                                    class="block w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 text-slate-900 font-mono font-black text-xl sm:text-2xl text-center uppercase tracking-[0.3em] placeholder:tracking-normal placeholder:font-sans placeholder:font-normal placeholder:text-slate-300 focus:border-[#2B77DE] focus:ring-4 focus:ring-blue-500/15 transition bg-white"
                                >
                            @endif

                            @error('token')
                                <p class="mt-2 text-xs font-semibold text-rose-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Call to Action (CTA) Button -->
                        @if($exam->hasNotStartedYet())
                            <button
                                type="button"
                                disabled
                                class="w-full py-3.5 px-6 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed select-none"
                            >
                                <span>Belum Bisa Dimulai</span>
                            </button>
                        @elseif($exam->hasEnded())
                            <button
                                type="button"
                                disabled
                                class="w-full py-3.5 px-6 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed select-none"
                            >
                                <span>Waktu Ujian Berakhir</span>
                            </button>
                        @else
                            <button
                                type="submit"
                                class="w-full py-3.5 px-6 rounded-2xl bg-[#2B77DE] hover:bg-[#1F67CB] text-white font-bold text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2 transition active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-[#2B77DE] focus:ring-offset-2 cursor-pointer"
                            >
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                <span>Mulai Ujian Now</span>
                            </button>
                        @endif
                    </form>

                    <!-- Secondary Action / Navigation -->
                    <a href="{{ route('siswa.dashboard') }}" 
                        class="flex justify-center items-center text-center pt-2 w-full py-3.5 px-6 rounded-2xl bg-slate-50 hover:bg-slate-100 text-xs sm:text-sm font-semibold text-slate-500 hover:text-[#2B77DE] transition inline-flex items-center gap-1.5 focus:outline-none focus:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Kembali ke Dashboard</span>
                    </a>

                </div>
            </div>

        </main>

    </div>
</x-app-layout>