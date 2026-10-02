<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs flex items-center">
                    <svg class="w-5 h-5 mr-2.5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-xs flex items-center">
                    <svg class="w-5 h-5 mr-2.5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Banner Ringkasan Info Ujian -->
            <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200/80 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Mata Pelajaran</span>
                    <p class="text-base font-extrabold text-slate-900 mt-0.5">{{ $exam->subject->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Target Kelas</span>
                    <p class="text-base font-extrabold text-slate-900 mt-0.5">{{ $exam->classroom->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Waktu Pengerjaan</span>
                    <p class="text-base font-extrabold text-slate-900 mt-0.5">{{ $exam->duration }} Menit</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Status Akses</span>
                    <div class="mt-0.5">
                        @if($exam->status === 'published')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Aktif (Siswa Dapat Mengerjakan)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                Draft (Belum Dimulai)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daftar Soal Ujian -->
            <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900">Daftar Soal Ujian ({{ $exam->questions->count() }} Soal)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Soal dapat memuat teks pertanyaan dan gambar penunjang.</p>
                    </div>

                    <div>
                        <a href="{{ route('guru.ujian.soal.create', $exam->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
                            + Tambah Soal Baru
                        </a>
                    </div>
                </div>

                @if($exam->questions->isEmpty())
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 mb-3 text-2xl">
                            <x-heroicon-s-pencil-square class="h-5 w-5" />
                        </div>
                        <h4 class="text-base font-bold text-slate-900">Ujian ini belum memiliki soal!</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tambahkan soal terlebih dahulu dengan mengklik tombol di bawah ini sebelum memulai ujian untuk siswa.</p>
                        <div class="mt-4">
                            <a href="{{ route('guru.ujian.soal.create', $exam->id) }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-indigo-700 shadow-xs">
                                + Input Soal Sekarang
                            </a>
                        </div>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($exam->questions as $index => $question)
                            <div class="p-6 sm:p-7 hover:bg-slate-50/50 transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4 flex-1">
                                        <!-- Nomor Soal -->
                                        <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-indigo-600 text-white font-black flex items-center justify-center text-xs shadow-xs">
                                            {{ $index + 1 }}
                                        </div>

                                        <!-- Konten Soal -->
                                        <div class="space-y-3 flex-1">
                                            <p class="text-slate-900 font-medium text-sm sm:text-base whitespace-pre-line leading-relaxed">
                                                {{ $question->question_text }}
                                            </p>

                                            <!-- Gambar Soal (Jika Ada) -->
                                            @if($question->image)
                                                <div class="my-3">
                                                    <a href="{{ asset('storage/' . $question->image) }}" target="_blank" class="inline-block group relative">
                                                        <img src="{{ asset('storage/' . $question->image) }}" alt="Gambar Soal" class="max-h-56 rounded-xl border border-slate-200 object-contain shadow-xs group-hover:opacity-90 transition">
                                                        <span class="absolute bottom-2 right-2 bg-black/70 text-white text-[10px] font-bold px-2 py-0.5 rounded">🔍 Perbesar</span>
                                                    </a>
                                                </div>
                                            @endif

                                            <!-- Pilihan Opsi Jawaban -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-2">
                                                @foreach(['A', 'B', 'C', 'D'] as $opt)
                                                    @php
                                                        $field = 'option_' . strtolower($opt);
                                                        $isKey = ($question->correct_answer === $opt);
                                                    @endphp
                                                    <div class="p-3 rounded-xl border text-xs sm:text-sm flex items-start gap-2.5 transition {{ $isKey ? 'border-emerald-500 bg-emerald-50/70 text-emerald-900 font-semibold shadow-2xs' : 'border-slate-200 bg-white text-slate-700' }}">
                                                        <span class="w-5 h-5 rounded-lg flex items-center justify-center text-xs font-bold {{ $isKey ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                                                            {{ $opt }}
                                                        </span>
                                                        <span class="flex-1">{{ $question->$field }}</span>
                                                        @if($isKey)
                                                            <span class="text-xs text-emerald-700 font-bold flex items-center ml-auto">
                                                                ✓ Kunci
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tombol Hapus Soal -->
                                    <div>
                                        <form action="{{ route('guru.soal.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus soal nomor {{ $index + 1 }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Hapus Soal">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
