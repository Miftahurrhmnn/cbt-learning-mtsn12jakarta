<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

        <!-- Guru Sidebar Component -->
        <x-guru-sidebar />

        <!-- ==================== MAIN CONTENT AREA ==================== -->
        <div class="flex-1 min-w-0 flex flex-col min-h-screen">
            
            <!-- Mobile Sticky Top Header -->
            <header class="md:hidden sticky top-0 z-20 flex items-center justify-between px-4 py-3 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <button @click="sidebarOpen = true" class="p-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-slate-900 shadow-2xs transition" aria-label="Buka Menu Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span class="font-extrabold text-slate-800 text-sm">Monitoring Siswa</span>
                </div>
                <a href="{{ route('guru.ujian.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
                    Daftar Ujian
                </a>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Portal Guru</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-indigo-600">Monitoring Siswa Realtime</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">

                <!-- Page Header Title & Subtitle -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Monitoring Pengerjaan Ujian Siswa</span>
                        </h1>
                    </div>
                    <div>
                        <button type="button" onclick="window.location.reload()" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold shadow-2xs transition">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Segarkan Data (Refresh)</span>
                        </button>
                    </div>
                </div>

                <!-- Stats Overview Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Sedang Mengerjakan -->
                    <div class="p-5 bg-white rounded-3xl border border-amber-200/80 shadow-2xs flex items-center gap-4 relative overflow-hidden">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 animate-spin text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-600 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                Sedang Mengerjakan
                            </span>
                            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalOngoing }} <span class="text-xs font-semibold text-slate-400">Siswa</span></p>
                        </div>
                    </div>

                    <!-- Selesai Ujian -->
                    <div class="p-5 bg-white rounded-3xl border border-emerald-200/80 shadow-2xs flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Selesai Mengerjakan</span>
                            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalCompleted }} <span class="text-xs font-semibold text-slate-400">Siswa</span></p>
                        </div>
                    </div>

                    <!-- Rata-rata Nilai -->
                    <div class="p-5 bg-white rounded-3xl border border-indigo-200/80 shadow-2xs flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Rata-rata Nilai Selesai</span>
                            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $avgScore }} <span class="text-xs font-semibold text-slate-400">/ 100</span></p>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search Toolbar -->
                <div class="p-4 sm:p-5 bg-white rounded-3xl border border-slate-200/80 shadow-2xs">
                    <form action="{{ route('guru.monitoring.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-center">
                        
                        <!-- Search Box (Nama / NISN) -->
                        <div class="sm:col-span-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari nama siswa, NISN, atau email..." 
                                class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        </div>

                        <!-- Filter Ujian -->
                        <div class="sm:col-span-3">
                            <select name="exam_id" onchange="this.form.submit()" 
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-white">
                                <option value="">Semua Ujian</option>
                                @foreach($exams as $ex)
                                    <option value="{{ $ex->id }}" {{ request('exam_id') == $ex->id ? 'selected' : '' }}>
                                        {{ Str::limit($ex->title, 26) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Kelas Siswa -->
                        <div class="sm:col-span-2">
                            <select name="classroom_id" onchange="this.form.submit()" 
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-white">
                                <option value="">Semua Kelas</option>
                                @foreach($classrooms as $cls)
                                    <option value="{{ $cls->id }}" {{ request('classroom_id') == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Status -->
                        <div class="sm:col-span-2">
                            <select name="status" onchange="this.form.submit()" 
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-white">
                                <option value="">Semua Status</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Sedang Mengerjakan</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <!-- Tombol Submit & Reset -->
                        <div class="sm:col-span-1 flex items-center gap-1.5">
                            <button type="submit" class="w-full p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition flex items-center justify-center shadow-xs" title="Cari">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </button>
                            @if(request()->hasAny(['search', 'exam_id', 'classroom_id', 'status']))
                                <a href="{{ route('guru.monitoring.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                                    ✕
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Students Monitoring Table -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <tr>
                                    <th class="py-3.5 px-4 sm:px-6">Nama Siswa & NISN</th>
                                    <th class="py-3.5 px-4">Kelas</th>
                                    <th class="py-3.5 px-4">Ujian & Mapel</th>
                                    <th class="py-3.5 px-4 text-center">Status</th>
                                    <th class="py-3.5 px-4 text-center">Waktu Mulai</th>
                                    <th class="py-3.5 px-4 text-center">Nilai Akhir</th>
                                    <th class="py-3.5 px-4 sm:px-6 text-right">Analisis Kotak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($sessions as $session)
                                    @php
                                        $user = $session->user;
                                        $exam = $session->exam;
                                        $nameParts = preg_split('/\s+/', trim($user->name ?? 'Siswa'));
                                        $initials = count($nameParts) >= 2 
                                            ? strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1))
                                            : strtoupper(mb_substr($nameParts[0], 0, 2));
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <!-- Siswa Info -->
                                        <td class="py-4 px-4 sm:px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                    {{ $initials }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-extrabold text-slate-900 truncate">{{ $user->name ?? '-' }}</p>
                                                    <p class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $user->nisn ?? $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Kelas -->
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                                                {{ $user->classroom->name ?? '-' }}
                                            </span>
                                        </td>

                                        <!-- Ujian & Mapel -->
                                        <td class="py-4 px-4">
                                            <p class="font-bold text-slate-900 truncate max-w-xs">{{ $exam->title ?? '-' }}</p>
                                            <span class="text-[11px] text-indigo-600 font-semibold">{{ $exam->subject->name ?? '-' }}</span>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-4 px-4 text-center">
                                            @if($session->status === 'ongoing')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Mengerjakan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                    Selesai
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Waktu Mulai -->
                                        <td class="py-4 px-4 text-center text-xs text-slate-500 font-mono">
                                            {{ $session->start_time ? $session->start_time->format('H:i:s') : '-' }}
                                            @if($session->end_time)
                                                <span class="block text-[10px] text-slate-400">Selesai: {{ $session->end_time->format('H:i:s') }}</span>
                                            @endif
                                        </td>

                                        <!-- Nilai Akhir -->
                                        <td class="py-4 px-4 text-center">
                                            @if($session->isCompleted())
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-base font-black text-slate-900">{{ number_format($session->score, 1) }}</span>
                                                    <span class="text-[10px] font-semibold text-slate-400">
                                                        {{ $session->correct_answers ?? 0 }} Benar / {{ $session->total_questions ?? 0 }} Soal
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-[11px] font-bold text-amber-600 italic">
                                                    Belum Selesai
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Aksi: Detail Analisis Jawaban Siswa -->
                                        <td class="py-4 px-4 sm:px-6 text-right">
                                            <a href="{{ route('guru.monitoring.detail', $session->id) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition shadow-2xs">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                                <span>Analisis Jawaban</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 px-6 text-center text-slate-400">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            </div>
                                            <p class="font-bold text-slate-700 text-sm">Tidak ada data sesi ujian siswa yang cocok</p>
                                            <p class="text-xs text-slate-400 mt-0.5">Siswa yang membuka token ujian akan muncul otomatis di daftar ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer -->
                    @if($sessions->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                            {{ $sessions->links() }}
                        </div>
                    @endif
                </div>

            </main>
        </div>
    </div>
</x-app-layout>
