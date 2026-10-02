<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    {{ __('Dashboard Siswa') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Selamat datang, <span class="font-bold text-indigo-600">{{ Auth::user()->name }}</span>. Selamat belajar dan mengerjakan ujian!</p>
            </div>
            <div class="inline-flex items-center px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-100 text-xs font-bold text-indigo-700">
                <span class="w-2 h-2 mr-2 bg-indigo-500 rounded-full animate-ping"></span>
                Portal CBT Aktif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2.5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl shadow-xs">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span class="font-medium text-sm">{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-xs">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2.5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @php
                $completedSessions = $mySessions->filter(fn($s) => $s->isCompleted());
                $avgScore = $completedSessions->count() > 0 ? $completedSessions->avg('score') : 0;
            @endphp

            <!-- Statistik Siswa -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                        <x-heroicon-o-signal class="h-5 w-5 text-emerald-600" />
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ujian Aktif</span>
                        <h4 class="text-2xl font-black text-slate-900 mt-0.5">{{ $activeExams->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                        <x-heroicon-s-check class="h-5 w-5" />
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ujian Diselesaikan</span>
                        <h4 class="text-2xl font-black text-slate-900 mt-0.5">{{ $completedSessions->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">
                        <x-heroicon-s-star class="h-5 w-5" />
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rata-Rata Nilai</span>
                        <h4 class="text-2xl font-black text-purple-700 mt-0.5">{{ number_format($avgScore, 1) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Section 1: Daftar Ujian yang Sedang Dibuka Guru -->
            <div>
                <div class="flex ml-4 sm:ml-0 md:ml-0 items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900">Ujian Yang Tersedia</h3>
                        <p class="text-xs text-slate-500">Pilih ujian untuk mulai mengerjakan. Waktu standar 1 jam.</p>
                    </div>
                </div>

                @if($activeExams->isEmpty())
                    <div class="bg-white p-8 sm:p-12 rounded-2xl shadow-xs border border-slate-200/80 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                            📂
                        </div>
                        <h4 class="text-base font-bold text-slate-900">Belum Ada Ujian yang Dibuka</h4>
                        <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Saat ini belum ada jadwal ujian yang dibuka oleh guru Anda. Silakan hubungi guru atau cek kembali berkala.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($activeExams as $exam)
                            @php
                                $session = $mySessions->get($exam->id);
                            @endphp
                            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                                <div class="p-6">
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700">
                                            {{ $exam->subject->name ?? 'Mata Pelajaran' }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $exam->classroom->name ?? 'Semua Kelas' }}
                                        </span>
                                    </div>

                                    <h4 class="font-bold text-base sm:text-lg text-slate-900 leading-snug">
                                        {{ $exam->title ?? 'Ujian ' . $exam->subject->name }}
                                    </h4>

                                    <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                                        <div class="flex items-center justify-between">
                                            <span class="flex items-center text-slate-500">
                                                <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Waktu Pengerjaan:
                                            </span>
                                            <strong class="text-slate-900 font-semibold">{{ $exam->duration }} Menit (1 Jam)</strong>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="flex items-center text-slate-500">
                                                <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Jumlah Soal:
                                            </span>
                                            <strong class="text-slate-900 font-semibold">{{ $exam->questions_count }} Soal</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5 bg-slate-50/70 border-t border-slate-100">
                                    @if(!$session)
                                        <a href="{{ route('siswa.ujian.token', $exam->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-xs transition" onclick="return confirm('Mulai ujian sekarang? Waktu 60 menit akan langsung berjalan.')">
                                            Mulai Kerjakan Ujian
                                        </a>
                                    @elseif($session->isCompleted())
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs font-bold text-emerald-700 flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                Selesai
                                            </span>
                                            <a href="{{ route('siswa.ujian.hasil', $exam->id) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded-lg text-xs font-black transition">
                                                Nilai: {{ number_format($session->score, 1) }} &rarr;
                                            </a>
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                            <div class="text-xs text-amber-700 font-bold flex items-center">
                                                <span class="w-2 h-2 mr-1.5 bg-amber-500 rounded-full animate-ping"></span>
                                                Sedang Dikerjakan
                                            </div>
                                            <a href="{{ route('siswa.ujian.show', $exam->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-xs transition">
                                                Lanjutkan Ujian &rarr;
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Section 2: Riwayat Nilai Ujian yang Telah Diselesaikan -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">Riwayat Nilai Ujian Saya</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Nilai hanya muncul jika ujian telah diselesaikan secara tuntas.</p>
                </div>

                @if($completedSessions->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-xs font-medium">
                        Belum ada riwayat ujian yang telah selesai.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Ujian</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu Selesai</th>
                                    <th class="px-6 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Jawaban Benar</th>
                                    <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Nilai Akhir</th>
                                    <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Rincian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($completedSessions as $s)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900 text-sm">
                                            {{ $s->exam->title ?? 'Ujian ' . $s->exam->subject->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 font-medium">
                                                {{ $s->exam->subject->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                            {{ $s->end_time ? $s->end_time->format('d M Y, H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-bold text-slate-700">
                                            {{ $s->correct_answers }} / {{ $s->total_questions }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-base font-black {{ $s->score >= 75 ? 'text-emerald-600' : 'text-indigo-600' }}">
                                                {{ number_format($s->score, 1) }}
                                            </span>
                                            <span class="text-xs text-slate-400">/ 100</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('siswa.ujian.hasil', $s->exam_id) }}" class="flex items-center justify-center gap-2 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                                Lihat Hasil  <x-heroicon-s-arrow-right class="h-3 w-3" /> 
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
