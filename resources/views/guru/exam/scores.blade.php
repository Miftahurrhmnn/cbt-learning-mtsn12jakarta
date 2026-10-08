<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

        <!-- Guru Sidebar Component (Desktop Sticky & Mobile Drawer) -->
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
                    <span class="font-extrabold text-slate-800 text-sm truncate max-w-[180px]">Rekap Nilai</span>
                </div>
                <a href="{{ route('guru.ujian.show', $exam->id) }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Detail Ujian</span>
                </a>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <a href="{{ route('guru.ujian.index') }}" class="hover:text-indigo-600 transition font-medium">Daftar Ujian</a>
                    <span>/</span>
                    <a href="{{ route('guru.ujian.show', $exam->id) }}" class="hover:text-indigo-600 transition font-medium truncate max-w-xs">{{ $exam->title ?? 'Detail Ujian' }}</a>
                    <span>/</span>
                    <span class="text-slate-700 font-bold">Rekapitulasi Nilai</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">
                <!-- Header Banner -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Rekapitulasi Nilai: {{ $exam->title ?? $exam->subject->name }}</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-3">
                            Kelas: <strong>{{ $exam->all_classroom_names }}</strong> 
                            <br>
                            Total Partisipasi: <strong>{{ $sessions->count() }} Siswa</strong>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('guru.ujian.show', $exam->id) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Detail</span>
                        </a>
                    </div>
                </div>

                <!-- Card Informasi Kebijakan Nilai -->
                <div class="p-4 bg-amber-50/90 border border-amber-200 text-amber-900 rounded-2xl shadow-2xs text-xs flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <span class="font-extrabold block text-amber-950 uppercase tracking-wider text-[11px]">Aturan Penilaian CBT</span>
                        <p class="mt-0.5 leading-relaxed text-amber-800">
                            Nilai siswa <strong>hanya akan keluar dan dihitung</strong> apabila siswa telah menyelesaikan seluruh proses ujian. Jika pengerjaan siswa belum selesai atau terputus, nilai tidak akan dirilis ke sistem guru maupun siswa.
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-2xs rounded-3xl border border-slate-200/80">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 uppercase">Daftar Pengerjaan Siswa</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Pantau status pengerjaan, skor perolehan, dan waktu penyelesaian siswa.</p>
                        </div>
                        <span class="px-3.5 py-1.5 bg-slate-50 text-slate-700 text-xs font-bold rounded-xl font-mono border border-indigo-100">
                            {{ $sessions->where('status', 'completed')->count() }} / {{ $sessions->count() }} Selesai
                        </span>
                    </div>

                    @if($sessions->isEmpty())
                        <div class="text-center py-16 px-4">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3 shadow-inner">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">Belum ada siswa yang mengerjakan</h4>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Pastikan status ujian telah diubah menjadi 'Aktif (Published)' agar siswa dapat memulai pengerjaan.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead>
                                    <tr class="bg-slate-50/70">
                                        <th class="px-6 py-3.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-400">No</th>
                                        <th class="px-6 py-3.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Nama Siswa</th>
                                        <th class="px-6 py-3.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Waktu Mulai</th>
                                        <th class="px-6 py-3.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Waktu Selesai</th>
                                        <th class="px-6 py-3.5 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Status</th>
                                        <th class="px-6 py-3.5 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Jawaban Benar</th>
                                        <th class="px-6 py-3.5 text-right text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Nilai Akhir</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @foreach($sessions as $index => $session)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-400">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-bold text-slate-900 text-xs">{{ $session->user->name ?? 'Siswa' }}</div>
                                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $session->user->email ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                                {{ $session->start_time ? $session->start_time->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                                {{ $session->end_time ? $session->end_time->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($session->isCompleted())
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                                        Belum Selesai (Sedang Dikerjakan)
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-semibold text-slate-700">
                                                @if($session->isCompleted())
                                                    {{ $session->correct_answers }} / {{ $session->total_questions }} Soal
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                @if($session->isCompleted())
                                                    <span class="text-base font-black {{ $session->score >= 75 ? 'text-emerald-600' : 'text-black' }}">
                                                        {{ number_format($session->score, 1) }}
                                                    </span>
                                                    <span class="text-xs text-slate-400 font-normal">/ 100</span>
                                                @else
                                                    <span class="text-xs text-rose-500 font-bold bg-rose-50 px-2.5 py-1 rounded-lg">
                                                        Nilai Belum Keluar
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Footer Note -->
                <footer class="pt-4 pb-8 text-center text-xs text-slate-400">
                    <p>&copy; {{ date('Y') }} CBT MTsN 12 Jakarta | Support by M1FDev</p>
                </footer>
            </main>
        </div>
    </div>
</x-app-layout>
