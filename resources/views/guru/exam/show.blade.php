<x-app-layout>
    <div class="py-8" x-data="{ showImportModal: false, docxFileName: '' }">
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

            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-xs text-xs space-y-1.5">
                    <div class="font-bold flex items-center gap-1.5 text-rose-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Peringatan Format Import:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5 text-rose-600">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('import_warnings') && count(session('import_warnings')) > 0)
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl shadow-xs text-xs space-y-1.5">
                    <div class="font-bold flex items-center gap-1.5 text-amber-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Catatan Hasil Import Soal:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5 text-amber-700">
                        @foreach(session('import_warnings') as $warn)
                            <li>{{ $warn }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Banner Ringkasan Info Ujian -->
            <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200/80 grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Mata Pelajaran</span>
                    <p class="text-base font-extrabold text-slate-900 mt-0.5">{{ $exam->subject->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Target Kelas</span>
                    <p class="text-base font-extrabold text-slate-900 mt-0.5">🏫 {{ $exam->classroom->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Jadwal Hari</span>
                    <p class="text-base font-extrabold text-indigo-700 mt-0.5">📅 {{ $exam->day_of_week ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Waktu Pengerjaan</span>
                    <p class="text-base font-extrabold text-slate-900 mt-0.5">{{ $exam->duration }} Menit</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Status & Token</span>
                    <div class="mt-0.5 flex flex-col gap-1">
                        @if($exam->status === 'published')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                Draft
                            </span>
                        @endif
                        @if($exam->token)
                            <span class="text-xs font-mono font-black text-slate-800">Token: <span class="text-indigo-600 font-bold">{{ $exam->token }}</span></span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daftar Soal Ujian -->
            <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900">Daftar Soal Ujian ({{ $exam->questions->count() }} Soal)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Soal dapat ditambahkan manual satu per satu atau diimpor massal dari file Word (.docx).</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Toggle Status Button -->
                        <form action="{{ route('guru.ujian.toggle_status', $exam->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition shadow-xs {{ $exam->status === 'published' ? 'bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                                <span>{{ $exam->status === 'published' ? 'Jadikan Draft' : 'Publikasikan Ujian' }}</span>
                            </button>
                        </form>

                        <!-- Rekap Nilai Button -->
                        <a href="{{ route('guru.ujian.scores', $exam->id) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 text-xs font-bold transition">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Rekap Nilai</span>
                        </a>

                        <!-- Tombol Import Template DOCX -->
                        <button type="button" @click="showImportModal = true" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-xl text-xs font-bold transition shadow-2xs">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                            <span>Import DOCX</span>
                        </button>

                        <!-- Tombol Tambah Soal Manual -->
                        <a href="{{ route('guru.ujian.soal.create', $exam->id) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl text-xs font-bold transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Input Manual</span>
                        </a>
                    </div>
                </div>

                @if($exam->questions->isEmpty())
                    <div class="p-10 text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 mb-3 text-2xl">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-900">Ujian ini belum memiliki soal!</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                            Anda dapat menambahkan soal satu per satu secara manual atau mengunggah banyak soal sekaligus menggunakan dokumen Microsoft Word (.docx).
                        </p>
                        
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <button type="button" @click="showImportModal = true" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                <span>Import Template DOCX</span>
                            </button>
                            <a href="{{ route('guru.ujian.soal.create', $exam->id) }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-xs transition">
                                <span>+ Input Soal Manual</span>
                            </a>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('guru.ujian.soal.template') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh Template Word (.docx) di sini</span>
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
        <!-- ==================== MODAL IMPORT SOAL DOCX ==================== -->
        <div x-show="showImportModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showImportModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showImportModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @keydown.escape.window="showImportModal = false"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-100">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Import Soal dari Word (.docx)</h3>
                                <p class="text-xs text-slate-500">{{ $exam->title ?? $exam->subject->name }} &bull; {{ $exam->classroom->name }}</p>
                            </div>
                        </div>
                        <button type="button" @click="showImportModal = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form action="{{ route('guru.ujian.soal.import_docx', $exam->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="p-6 space-y-5">
                            
                            <!-- Box Unduh Template -->
                            <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="text-xs font-bold text-indigo-900 block">Belum punya format dokumen?</span>
                                    <span class="text-[11px] text-indigo-700/80">Unduh template tabel resmi agar soal dikenali sistem.</span>
                                </div>
                                <a href="{{ route('guru.ujian.soal.template') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-2xs shrink-0 self-start sm:self-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh Template</span>
                                </a>
                            </div>

                            <!-- Upload Input Dropzone -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Pilih Dokumen Word (.docx) <span class="text-rose-500">*</span>
                                </label>
                                <div class="border-2 border-dashed border-slate-300 hover:border-indigo-500 rounded-2xl p-6 text-center bg-slate-50/50 hover:bg-indigo-50/30 transition relative cursor-pointer group">
                                    <input type="file" name="docx_file" id="docx_file" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required
                                           @change="if ($event.target.files.length) { docxFileName = $event.target.files[0].name; }"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    
                                    <div class="flex flex-col items-center pointer-events-none">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <template x-if="!docxFileName">
                                            <div>
                                                <span class="text-xs font-bold text-slate-700 block">Klik atau Seret file .docx ke area ini</span>
                                                <span class="text-[11px] text-slate-400 mt-1 block">Mendukung Microsoft Word format .docx (Maks. 15MB)</span>
                                            </div>
                                        </template>
                                        <template x-if="docxFileName">
                                            <div class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                                                📄 <span x-text="docxFileName"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                @error('docx_file')
                                    <p class="text-xs text-rose-600 mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Petunjuk Singkat -->
                            <div class="text-[11px] text-slate-500 bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 space-y-1">
                                <p class="font-bold text-slate-700">Ketentuan Dokumen:</p>
                                <ul class="list-disc pl-4 space-y-0.5 text-slate-500">
                                    <li>Format tabel dengan kolom: <em>No, Pertanyaan, Pilihan A, B, C, D, Kunci</em>.</li>
                                    <li>Kunci jawaban harus ditulis huruf kapital <strong>A, B, C, atau D</strong>.</li>
                                    <li>Gambar pada soal dapat disalin/ditempel langsung ke kolom pertanyaan pada Word.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="px-6 py-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="button" @click="showImportModal = false" 
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider shadow-xs transition hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span>Mulai Impor Soal</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
