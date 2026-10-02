<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r shadow-xs">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(!$isCompleted)
                <!-- Tampilan Jika Ujian BELUM SELESAI (Nilai Tidak Keluar) -->
                <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-8 text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900">Ujian Belum Selesai</h3>
                    <p class="text-gray-600 max-w-md mx-auto text-sm leading-relaxed">
                        Nilai tidak dapat ditampilkan karena Anda belum menyelesaikan proses ujian ini secara tuntas.
                    </p>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-left text-xs space-y-1.5 max-w-md mx-auto text-gray-600">
                        <div><strong>Mata Pelajaran:</strong> {{ $exam->subject->name ?? '-' }}</div>
                        <div><strong>Kelas:</strong> {{ $exam->classroom->name ?? '-' }}</div>
                        <div><strong>Status Nilai:</strong> <span class="text-rose-500 font-semibold italic">Disembunyikan (Ujian Belum Selesai)</span></div>
                    </div>

                    <div class="pt-4 flex items-center justify-center gap-3">
                        <a href="{{ route('siswa.dashboard') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                            Ke Dashboard
                        </a>
                        <a href="{{ route('siswa.ujian.show', $exam->id) }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow-xs transition">
                            Lanjutkan Ujian
                        </a>
                    </div>
                </div>

            @else
                <!-- Tampilan Jika Ujian TELAH SELESAI (Nilai Ditampilkan) -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <!-- Header Kartu Nilai -->
                    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-800 text-white p-8 text-center relative overflow-hidden">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white mb-3">
                            <svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Ujian Telah Selesai
                        </span>
                        <h3 class="text-2xl font-black">{{ $exam->title ?? 'Ujian ' . $exam->subject->name }}</h3>
                        <p class="text-xs text-indigo-100 mt-1">{{ $exam->subject->name }} &bull; {{ $exam->classroom->name }}</p>

                        <!-- Angka Nilai -->
                        <div class="mt-6 inline-flex flex-col items-center justify-center w-36 h-36 rounded-full bg-white text-indigo-950 shadow-xl border-4 border-indigo-200">
                            <span class="text-4xl font-black tracking-tight {{ $score >= 75 ? 'text-emerald-600' : 'text-indigo-600' }}">
                                {{ number_format($score, 1) }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider mt-0.5">Nilai Akhir</span>
                        </div>
                    </div>

                    <!-- Rincian Skor -->
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="grid grid-cols-3 gap-3 sm:gap-4 text-center">
                            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                                <span class="block text-2xl font-black text-emerald-600">
                                    {{ $session->correct_answers }}
                                </span>
                                <span class="text-xs text-slate-600 font-semibold">Jawaban Benar</span>
                            </div>

                            <div class="p-4 bg-rose-50 rounded-2xl border border-rose-100">
                                <span class="block text-2xl font-black text-rose-600">
                                    {{ $session->total_questions - $session->correct_answers }}
                                </span>
                                <span class="text-xs text-slate-600 font-semibold">Jawaban Salah</span>
                            </div>

                            <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                                <span class="block text-2xl font-black text-indigo-600">
                                    {{ $session->total_questions }}
                                </span>
                                <span class="text-xs text-slate-600 font-semibold">Total Soal</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-500">
                            <div class="flex justify-between">
                                <span>Waktu Mulai:</span>
                                <strong class="text-slate-700">{{ $session->start_time ? $session->start_time->format('d M Y, H:i:s') : '-' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Waktu Selesai:</span>
                                <strong class="text-slate-700">{{ $session->end_time ? $session->end_time->format('d M Y, H:i:s') : '-' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Status Sistem:</span>
                                <strong class="text-emerald-600">Selesai & Tersimpan di Buku Nilai Guru</strong>
                            </div>
                        </div>

                        <div class="pt-2 flex items-center justify-center gap-3">
                            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-md shadow-indigo-100 transition hover:-translate-y-0.5">
                                &larr; Kembali ke Dashboard Ujian
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pembahasan & Kunci Jawaban Soal (Fitur Baru: Review Jawaban Siswa & Kunci Benar) -->
                @if(isset($questions) && $questions->isNotEmpty())
                    <div class="mt-10 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-black text-slate-900">Pembahasan & Lembar Jawaban</h4>
                            </div>
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-xs font-bold">
                                {{ $questions->count() }} Butir Soal
                            </span>
                        </div>

                        <div class="space-y-4">
                            @foreach($questions as $index => $q)
                                @php
                                    $ans = $userAnswers->get($q->id);
                                    $selected = $ans ? $ans->selected_answer : null;
                                    $isCorrect = $ans ? $ans->is_correct : false;
                                @endphp
                                <div class="bg-white rounded-2xl border {{ $isCorrect ? 'border-emerald-200' : 'border-rose-200' }} p-5 sm:p-6 shadow-xs space-y-4 transition">
                                    <!-- Header Soal -->
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl text-xs font-black {{ $isCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="text-xs font-extrabold {{ $isCorrect ? 'text-emerald-700' : 'text-rose-700' }}">
                                                {{ $isCorrect ? '✓ Jawaban Anda Benar' : '✕ Jawaban Anda Salah' }}
                                            </span>
                                        </div>

                                        <div class="text-xs font-semibold">
                                            @if($selected)
                                                <span class="text-slate-500">Pilihan Anda: <strong class="{{ $isCorrect ? 'text-emerald-600' : 'text-rose-600 font-black' }}">{{ $selected }}</strong></span>
                                            @else
                                                <span class="text-amber-600 font-bold italic">Tidak Dijawab</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Teks Soal -->
                                    <div class="text-sm font-semibold text-slate-900 leading-relaxed">
                                        {{ $q->question_text }}
                                    </div>

                                    <!-- Gambar Soal Jika Ada -->
                                    @if($q->image)
                                        <div class="my-3">
                                            <img src="{{ asset('storage/' . $q->image) }}" alt="Gambar Soal" class="max-h-64 rounded-xl border border-slate-200 object-contain bg-slate-50 p-1">
                                        </div>
                                    @endif

                                    <!-- Opsi Jawaban A, B, C, D -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                        @foreach(['A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c, 'D' => $q->option_d] as $key => $text)
                                            @php
                                                $isKeyCorrect = ($key === $q->correct_answer);
                                                $isKeySelected = ($key === $selected);
                                            @endphp
                                            <div class="p-3 rounded-xl border text-xs flex items-start gap-2.5 transition
                                                @if($isKeyCorrect && $isKeySelected)
                                                    bg-emerald-50 border-emerald-400 text-emerald-950 font-bold ring-2 ring-emerald-500/20
                                                @elseif($isKeyCorrect)
                                                    bg-emerald-50/80 border-emerald-300 text-emerald-900 font-bold
                                                @elseif($isKeySelected)
                                                    bg-rose-50 border-rose-300 text-rose-950 font-bold
                                                @else
                                                    bg-slate-50/50 border-slate-200 text-slate-600
                                                @endif">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs font-black shrink-0
                                                    @if($isKeyCorrect) bg-emerald-600 text-white
                                                    @elseif($isKeySelected) bg-rose-600 text-white
                                                    @else bg-slate-200 text-slate-700 @endif">
                                                    {{ $key }}
                                                </span>
                                                <div class="flex-1 min-w-0 pt-0.5">
                                                    <span>{{ $text }}</span>
                                                    @if($isKeyCorrect && $isKeySelected)
                                                        <span class="block mt-1 text-[11px] text-emerald-700 font-black">✓ Jawaban Anda (Benar)</span>
                                                    @elseif($isKeyCorrect)
                                                        <span class="block mt-1 text-[11px] text-emerald-700 font-black">★ Kunci Jawaban yang Benar</span>
                                                    @elseif($isKeySelected)
                                                        <span class="block mt-1 text-[11px] text-rose-600 font-black">✕ Pilihan Anda (Salah)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Footer Baris Status -->
                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-500">
                                            Kunci Jawaban: <strong class="text-emerald-700 font-black">{{ $q->correct_answer }}</strong>
                                        </span>
                                        <span class="text-slate-400 font-medium">
                                            Poin: {{ $isCorrect ? '1 / 1' : '0 / 1' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
