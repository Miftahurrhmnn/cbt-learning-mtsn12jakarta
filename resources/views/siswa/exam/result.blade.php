<x-app-layout :hide-nav="true">
    <div x-data="{ profileModalOpen: false }" 
         class="min-h-screen bg-slate-100/70 font-sans text-slate-800 antialiased pb-28 md:pb-16 flex flex-col justify-between selection:bg-blue-500 selection:text-white">

        <!-- ==================== TOP BLUE APP HEADER (Selaras dengan Dashboard Siswa) ==================== -->
        <header class="bg-[#2B77DE] bg-gradient-to-b from-[#2B77DE] to-[#1F67CB] text-white pt-4 pb-16 px-4 sm:px-6 lg:px-8 rounded-b-[2.5rem] shadow-md relative overflow-hidden">
            <!-- Background Decorative Circles -->
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute top-1/2 -left-10 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>

            <div class="max-w-3xl mx-auto relative z-10 space-y-3">
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
                            {{ Auth::user()->nisn ?? 'Siswa MTsN 12' }} &bull; {{ Auth::user()->classroom->name ?? 'Kelas Siswa' }}
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- ==================== MAIN CARD AREA ==================== -->
        <main class="max-w-2xl w-full mx-auto px-4 sm:px-6 -mt-10 relative z-20 flex-1 flex flex-col justify-center">

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-500 text-white rounded-2xl shadow-md flex items-center gap-3 text-xs sm:text-sm font-semibold">
                    <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(!$isCompleted)
                <!-- ==================== KONDISI: UJIAN BELUM SELESAI ==================== -->
                <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white p-7 sm:p-9 shadow-xl shadow-slate-200/70 text-center space-y-5">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto shadow-xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>

                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900">Ujian Belum Selesai</h3>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mt-1.5 leading-relaxed">
                            Nilai tidak dapat ditampilkan karena Anda belum menyelesaikan proses ujian ini secara tuntas.
                        </p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-left text-xs space-y-2 max-w-sm mx-auto text-slate-600">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Mata Pelajaran:</span>
                            <strong class="text-slate-800">{{ $exam->subject->name ?? '-' }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Kelas Ujian:</span>
                            <strong class="text-slate-800">{{ $exam->classroom->name ?? '-' }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status Nilai:</span>
                            <strong class="text-amber-600">Belum Selesai</strong>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('siswa.dashboard') }}" class="w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs uppercase tracking-wider transition text-center">
                            Ke Dashboard
                        </a>
                        <a href="{{ route('siswa.ujian.show', $exam->id) }}" class="w-full sm:w-auto px-7 py-3 bg-[#2B77DE] hover:bg-[#1F67CB] text-white font-bold rounded-2xl text-xs uppercase tracking-wider shadow-md shadow-blue-500/20 transition text-center">
                            Lanjutkan Ujian
                        </a>
                    </div>
                </div>

            @else
                <!-- ==================== KONDISI: UJIAN TELAH SELESAI ==================== -->
                <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-xl shadow-slate-200/70">
                    
                    <!-- Card Top Header -->
                    <div class="border-b border-slate-100 bg-gradient-to-b from-slate-50/90 to-white px-6 py-6 text-center">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Ujian Telah Selesai
                        </div>
                        <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                            {{ $exam->title ?? 'Ujian ' . ($exam->subject->name ?? '') }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $exam->subject->name ?? '-' }} &bull; {{ $exam->classroom->name ?? '-' }}
                        </p>

                        <!-- Nilai Hero Circle -->
                        <div class="mt-6 mb-2 inline-flex flex-col items-center justify-center w-32 h-32 sm:w-36 sm:h-36 rounded-3xl bg-gradient-to-tr from-blue-500 to-[#2B77DE] text-white shadow-lg shadow-blue-500/25 border-4 border-white">
                            <span class="text-4xl sm:text-5xl font-black tracking-tight leading-none">
                                {{ number_format($score, 1) }}
                            </span>
                            <span class="text-[10px] sm:text-[11px] text-blue-100 font-extrabold uppercase tracking-widest mt-1.5">
                                Nilai Akhir
                            </span>
                        </div>
                    </div>

                    <!-- Summary Stat Cards: Benar, Salah, Total Soal -->
                    <div class="p-6 sm:p-7 space-y-6">
                        
                        <div class="grid grid-cols-3 gap-3 sm:gap-4 text-center">
                            <!-- Jawaban Benar -->
                            <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-emerald-950 flex flex-col items-center justify-center transition hover:bg-emerald-50">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm mb-2 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-2xl sm:text-3xl font-black text-emerald-600 leading-none">
                                    {{ $correctAnswers }}
                                </span>
                                <span class="text-[11px] sm:text-xs font-bold text-emerald-800 mt-1.5">
                                    Jawaban Benar
                                </span>
                            </div>

                            <!-- Jawaban Salah -->
                            <div class="p-4 sm:p-5 rounded-2xl bg-rose-50/70 border border-rose-100 text-rose-950 flex flex-col items-center justify-center transition hover:bg-rose-50">
                                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-sm mb-2 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <span class="text-2xl sm:text-3xl font-black text-rose-600 leading-none">
                                    {{ $wrongAnswers }}
                                </span>
                                <span class="text-[11px] sm:text-xs font-bold text-rose-800 mt-1.5">
                                    Jawaban Salah
                                </span>
                            </div>

                            <!-- Total Soal -->
                            <div class="p-4 sm:p-5 rounded-2xl bg-blue-50/70 border border-blue-100 text-blue-950 flex flex-col items-center justify-center transition hover:bg-blue-50">
                                <div class="w-8 h-8 rounded-xl bg-[#2B77DE] text-white flex items-center justify-center font-bold text-sm mb-2 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <span class="text-2xl sm:text-3xl font-black text-[#2B77DE] leading-none">
                                    {{ $totalQuestions }}
                                </span>
                                <span class="text-[11px] sm:text-xs font-bold text-blue-800 mt-1.5">
                                    Total Soal
                                </span>
                            </div>
                        </div>

                        <!-- Info Waktu & Konfirmasi Penyimpanan -->
                        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 space-y-2 text-xs text-slate-500">
                            <div class="flex justify-between items-center">
                                <span>Waktu Mulai:</span>
                                <strong class="text-slate-700 font-mono">{{ $session->start_time ? $session->start_time->format('H:i:s') : '-' }} WIB</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Waktu Selesai:</span>
                                <strong class="text-slate-700 font-mono">{{ $session->end_time ? $session->end_time->format('H:i:s') : '-' }} WIB</strong>
                            </div>
                            <div class="flex justify-between items-center pt-1 border-t border-slate-200/60">
                                <span>Status Nilai:</span>
                                <strong class="text-emerald-600 flex items-center gap-1 font-bold">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Tersimpan di Buku Nilai Guru
                                </strong>
                            </div>
                        </div>

                        <!-- CTA Button Kembali ke Dashboard -->
                        <div class="pt-2">
                            <a href="{{ route('siswa.dashboard') }}" 
                               class="w-full py-3.5 px-6 rounded-2xl bg-[#2B77DE] hover:bg-[#1F67CB] text-white font-bold text-xs sm:text-sm uppercase tracking-wider shadow-md shadow-blue-500/25 flex items-center justify-center gap-2.5 transition active:scale-[0.99] cursor-pointer">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                                <span>Kembali ke Dashboard Siswa</span>
                            </a>
                        </div>

                    </div>
                </div>
            @endif

        </main>

        <!-- ==================== MODAL PROFIL SISWA ==================== -->
        <div x-show="profileModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click="profileModalOpen = false">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative text-center space-y-4" @click.stop>
                <div class="w-16 h-16 rounded-full bg-[#2B77DE] text-white flex items-center justify-center mx-auto text-xl font-black shadow-md">
                    {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 2)) }}
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
                        <strong class="text-blue-700">{{ Auth::user()->classroom->name ?? '-' }}</strong>
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

        <!-- ==================== FLOATING BOTTOM APP NAVIGATION DOCK ==================== -->
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
