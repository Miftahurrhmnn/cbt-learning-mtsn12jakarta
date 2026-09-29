<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('guru.ujian.show', $exam->id) }}" class="text-indigo-600 hover:text-indigo-800">&larr; Kembali ke Ujian</a>
                    <span>/</span>
                    <span>Tambah Soal</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Input Soal: {{ $exam->title ?? $exam->subject->name }}
                </h2>
                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold">
                        📖 {{ $exam->subject->name }}
                    </span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold">
                        🏫 {{ $exam->classroom->name }}
                    </span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8" x-data="{ imagePreview: null, selectedKey: '{{ old('correct_answer', 'A') }}' }">
            <div class="bg-white overflow-hidden shadow-xs rounded-3xl border border-slate-200/80">
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
                                🖼️ Gambar Soal (Opsional)
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
                                    class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md hover:bg-rose-600 text-xs font-bold">
                                    ✕
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
                                    <span class="text-[10px] mt-0.5" x-text="selectedKey === '{{ $opt }}' ? '✓ Kunci' : 'Pilihan'"></span>
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
                            Simpan Soal ke Bank Ujian &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
