<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('guru.ujian.show', $exam->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                        &larr; Detail Ujian
                    </a>
                    <span class="text-xs text-slate-300">&bull;</span>
                    <span class="text-xs font-semibold text-slate-500">Nilai & Hasil</span>
                </div>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">
                    Rekapitulasi Nilai: {{ $exam->title ?? $exam->subject->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Kelas: <strong>{{ $exam->classroom->name }}</strong> &bull; Total Peserta: <strong>{{ $sessions->count() }} Siswa</strong>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('guru.ujian.show', $exam->id) }}" 
                   class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card Informasi Kebijakan Nilai -->
            <div class="p-4 bg-amber-50/90 border border-amber-200 text-amber-900 rounded-2xl shadow-2xs text-xs flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
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
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase">Daftar Pengerjaan Siswa</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pantau status pengerjaan, skor perolehan, dan waktu penyelesaian siswa.</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full font-mono">
                        {{ $sessions->where('status', 'completed')->count() }} / {{ $sessions->count() }} Selesai
                    </span>
                </div>

                @if($sessions->isEmpty())
                    <div class="text-center py-16 px-4">
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3">
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
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    ✓ Selesai
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
                                                <span class="text-base font-black {{ $session->score >= 75 ? 'text-emerald-600' : 'text-indigo-600' }}">
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
        </div>
    </div>
</x-app-layout>
