<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false, activeTab: 'manual', imagePreview: null, selectedKey: '{{ old('correct_answer', 'A') }}', docxFileName: '' }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

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
                    <span class="font-extrabold text-slate-800 text-sm">Tambah Soal Ujian</span>
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
                    <span class="text-slate-700 font-bold">Tambah Soal</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-4xl w-full mx-auto">
                <!-- Header Title -->
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Butir Soal Baru</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Paket: <strong>{{ $exam->title ?? $exam->subject->name }}</strong> &bull; Kelas: <strong>{{ $exam->all_classroom_names }}</strong>
                    </p>
                </div>

                <!-- Tab Switcher: Input Manual vs Import DOCX -->
                <div class="flex items-center p-1.5 bg-slate-200/70 rounded-2xl max-w-md mx-auto">
                    <button type="button" @click="activeTab = 'manual'"
                        class="flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition text-center flex items-center justify-center gap-2"
                        :class="activeTab === 'manual' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Input Manual</span>
                    </button>
                    <button type="button" @click="activeTab = 'import'"
                        class="flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition text-center flex items-center justify-center gap-2"
                        :class="activeTab === 'import' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Import DOCX Template</span>
                    </button>
                </div>

                <!-- ==================== TAB 1: FORM INPUT MANUAL ==================== -->
                <div x-show="activeTab === 'manual'" class="bg-white overflow-hidden shadow-2xs rounded-3xl border border-slate-200/80">
                    <form action="{{ route('guru.ujian.soal.store', $exam->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                        @csrf

                        <!-- Teks Pertanyaan Soal -->
                        <div>
                            <label for="question_text" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Teks Pertanyaan Soal <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="question_text" id="question_text" rows="4" required
                                placeholder="Tuliskan pertanyaan soal di sini secara lengkap..."
                                class="block w-full px-4 py-3 rounded-2xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs leading-relaxed">{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload Gambar Soal (Opsional) -->
                        <div class="p-5 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/60 hover:bg-slate-50 transition">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Gambar Soal (Opsional)
                                </label>
                                <span class="text-[11px] text-slate-400 font-medium">Format: JPG, PNG, WEBP (Maks 2MB)</span>
                            </div>
                            <p class="text-xs text-slate-500 mb-3 leading-normal">
                                Gunakan untuk soal yang memerlukan diagram, peta, grafik rumus, atau foto ilustrasi.
                            </p>

                            <input type="file" name="image" id="image" accept="image/*"
                                @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); } else { imagePreview = null; }"
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">

                            <!-- Preview Gambar Realtime -->
                            <div x-show="imagePreview" class="mt-4" style="display: none;">
                                <span class="block text-xs font-bold text-slate-600 mb-1.5">Preview Gambar:</span>
                                <div class="relative inline-block">
                                    <img :src="imagePreview" class="max-h-52 rounded-xl border border-slate-300 shadow-xs object-contain bg-white">
                                    <button type="button" @click="imagePreview = null; document.getElementById('image').value = ''"
                                        class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md hover:bg-rose-600 transition">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>

                            @error('image')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pilihan Jawaban A, B, C, D -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                Pilihan Jawaban (A, B, C, D)
                            </h4>

                            @foreach(['A', 'B', 'C', 'D'] as $opt)
                                @php $fieldName = 'option_' . strtolower($opt); @endphp
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Pilihan {{ $opt }} <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center">
                                                {{ $opt }}
                                            </span>
                                        </div>
                                        <input type="text" name="{{ $fieldName }}" value="{{ old($fieldName) }}" required
                                            placeholder="Ketik teks jawaban pilihan {{ $opt }}"
                                            class="block w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
                                    </div>
                                    @error($fieldName)
                                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <!-- Kunci Jawaban Benar Interaktif -->
                        <div class="p-5 bg-emerald-50/60 rounded-2xl border border-emerald-100 space-y-2.5">
                            <label class="block text-xs font-black text-emerald-950 uppercase tracking-wider">
                                Tentukan Kunci Jawaban yang Benar <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-emerald-800">Pilih salah satu opsi di bawah sebagai jawaban benar untuk penghitungan skor otomatis.</p>
                            
                            <div class="grid grid-cols-4 gap-3 pt-1">
                                @foreach(['A', 'B', 'C', 'D'] as $opt)
                                    <label class="flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition"
                                        :class="selectedKey === '{{ $opt }}' ? 'border-emerald-600 bg-emerald-600 text-white shadow-xs font-black' : 'border-slate-200 hover:border-slate-300 bg-white text-slate-700 font-bold'">
                                        <input type="radio" name="correct_answer" value="{{ $opt }}" class="sr-only" x-model="selectedKey">
                                        <span class="text-base font-black">{{ $opt }}</span>
                                        <span class="text-[10px] mt-0.5 inline-flex items-center gap-0.5">
                                            <template x-if="selectedKey === '{{ $opt }}'">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                            <span x-text="selectedKey === '{{ $opt }}' ? 'Kunci' : 'Pilihan'"></span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('correct_answer')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('guru.ujian.show', $exam->id) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition hover:shadow-md">
                                Simpan Soal ke Bank Ujian
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ==================== TAB 2: IMPORT DOCX TEMPLATE ==================== -->
                <div x-show="activeTab === 'import'" class="bg-white overflow-hidden shadow-2xs rounded-3xl border border-slate-200/80 p-6 sm:p-8 space-y-6" style="display: none;">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900">Import Soal Massal dari Dokumen Word (.docx)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Unggah banyak soal sekaligus menggunakan dokumen Word yang telah diisi sesuai format tabel.
                        </p>
                    </div>

                    <!-- Langkah 1: Download Template -->
                    <div class="p-5 rounded-2xl bg-indigo-50/80 border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-xs font-bold">
                                1
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Unduh Template Resmi</h4>
                                <p class="text-xs text-slate-600 mt-0.5">Gunakan format tabel resmi agar butir soal, opsi A-D, dan kunci jawaban terbaca dengan akurat.</p>
                            </div>
                        </div>
                        <a href="{{ route('guru.ujian.soal.template') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-xs self-start sm:self-auto shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Template (.docx)</span>
                        </a>
                    </div>

                    <!-- Langkah 2: Upload File DOCX -->
                    <form action="{{ route('guru.ujian.soal.import_docx', $exam->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs font-bold">
                                2
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Unggah File Word yang Telah Diisi</h4>
                                <p class="text-xs text-slate-600 mt-0.5">Pilih dokumen .docx yang sudah Anda isi sesuai tabel template.</p>
                            </div>
                        </div>

                        <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-8 text-center bg-slate-50/50 hover:bg-blue-50/20 transition relative cursor-pointer group">
                            <input type="file" name="docx_file" id="tab_docx_file" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required
                                   @change="if ($event.target.files.length) { docxFileName = $event.target.files[0].name; }"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <div class="flex flex-col items-center pointer-events-none">
                                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <template x-if="!docxFileName">
                                    <div>
                                        <span class="text-sm font-bold text-slate-700 block">Klik atau Seret file .docx ke sini</span>
                                        <span class="text-xs text-slate-400 mt-1 block">Format: Microsoft Word .docx (Maksimal 15MB)</span>
                                    </div>
                                </template>
                                <template x-if="docxFileName">
                                    <div class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-200">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span x-text="docxFileName"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('docx_file')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror

                        <!-- Petunjuk Pengisian Tabel -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-2">
                            <span class="font-bold text-slate-800 block">Struktur Kolom Tabel Template:</span>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border border-slate-200 rounded-lg text-[11px]">
                                    <thead class="bg-slate-100 font-bold text-slate-700">
                                        <tr>
                                            <th class="p-2 border-b">No</th>
                                            <th class="p-2 border-b">Pertanyaan Soal</th>
                                            <th class="p-2 border-b">Pilihan A</th>
                                            <th class="p-2 border-b">Pilihan B</th>
                                            <th class="p-2 border-b">Pilihan C</th>
                                            <th class="p-2 border-b">Pilihan D</th>
                                            <th class="p-2 border-b">Kunci</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-slate-500">
                                        <tr>
                                            <td class="p-2 border-b">1</td>
                                            <td class="p-2 border-b">Ibu kota negara Indonesia adalah...</td>
                                            <td class="p-2 border-b">Jakarta</td>
                                            <td class="p-2 border-b">Surabaya</td>
                                            <td class="p-2 border-b">Bandung</td>
                                            <td class="p-2 border-b">Medan</td>
                                            <td class="p-2 border-b font-bold text-emerald-600">A</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 italic">
                                * Kolom Kunci Jawaban WAJIB berupa huruf kapital (A, B, C, atau D).
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <a href="{{ route('guru.ujian.show', $exam->id) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Unggah & Mulai Impor Soal</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer Note -->
                <footer class="pt-4 pb-8 text-center text-xs text-slate-400">
                    <p>&copy; {{ date('Y') }} CBT MTsN 12 Jakarta | Support by M1FDev</p>
                </footer>
            </main>
        </div>
    </div>
</x-app-layout>
