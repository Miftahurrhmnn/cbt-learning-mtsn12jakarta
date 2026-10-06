<x-app-layout :hide-nav="true">
    <div x-data="{ 
        sidebarOpen: false, 
        imagePreview: null, 
        selectedKey: '{{ old('correct_answer', $question->correct_answer) }}', 
        removeImage: false 
    }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

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
                    <span class="font-extrabold text-slate-800 text-sm">Edit Butir Soal</span>
                </div>
                <a href="{{ $exam ? route('guru.ujian.show', $exam->id) : route('guru.bank-soal.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-end px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-4xl w-full mx-auto">
                <!-- Page Breadcrumbs & Header -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                            @if($exam)
                                <a href="{{ route('guru.ujian.index') }}" class="hover:text-indigo-600 transition">Daftar Ujian</a>
                                <span>/</span>
                                <a href="{{ route('guru.ujian.show', $exam->id) }}" class="hover:text-indigo-600 transition">{{ $exam->title }}</a>
                                <span>/</span>
                            @else
                                <a href="{{ route('guru.bank-soal.index') }}" class="hover:text-indigo-600 transition">Bank Soal</a>
                                <span>/</span>
                            @endif
                            <span class="text-slate-600 font-semibold">Edit Soal</span>
                        </div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Perbarui Butir Soal</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Anda dapat mengubah pertanyaan teks, mengunggah ulang atau menghapus gambar, serta memperbarui opsi dan kunci jawaban.
                        </p>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white overflow-hidden shadow-2xs rounded-3xl border border-slate-200/80">
                    <form action="{{ route('guru.soal.update', $question->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Teks Pertanyaan Soal -->
                        <div>
                            <label for="question_text" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Teks Pertanyaan Soal <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="question_text" id="question_text" rows="4" required
                                placeholder="Tuliskan butir soal di sini secara lengkap..."
                                class="block w-full px-4 py-3 rounded-2xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs leading-relaxed">{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gambar Soal -->
                        <div class="p-5 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/60 hover:bg-slate-50 transition">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Gambar Soal (Diagram / Ilustrasi)
                                </label>
                                <span class="text-[11px] text-slate-400 font-medium">Format: JPG, PNG, WEBP (Maks 2MB)</span>
                            </div>

                            @if($question->image)
                                <div class="mb-4 p-3 bg-white rounded-xl border border-slate-200 inline-block" x-show="!removeImage">
                                    <p class="text-[11px] font-bold text-slate-500 mb-1.5">Gambar Saat Ini:</p>
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $question->image) }}" alt="Gambar Soal" class="max-h-48 rounded-lg object-contain bg-slate-50 border">
                                    </div>
                                    <label class="mt-2.5 flex items-center gap-2 cursor-pointer text-xs font-semibold text-rose-600 hover:text-rose-700">
                                        <input type="checkbox" name="remove_image" value="1" x-model="removeImage" class="rounded text-rose-600 focus:ring-rose-500">
                                        <span>Centang untuk menghapus gambar ini</span>
                                    </label>
                                </div>
                            @endif

                            <div x-show="removeImage" class="mb-3 p-2.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-medium text-rose-700 flex items-center justify-between">
                                <span>Gambar akan dihapus saat disimpan.</span>
                                <button type="button" @click="removeImage = false" class="underline font-bold text-rose-800">Batalkan Hapus</button>
                            </div>

                            <p class="text-xs text-slate-500 mb-3 leading-normal">
                                Pilih gambar baru di bawah jika ingin mengganti ilustrasi soal:
                            </p>

                            <input type="file" name="image" id="image" accept="image/*"
                                @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); } else { imagePreview = null; }"
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">

                            <!-- Preview Gambar Baru Realtime -->
                            <div x-show="imagePreview" class="mt-4" style="display: none;">
                                <span class="block text-xs font-bold text-indigo-700 mb-1.5">Preview Gambar Baru Yang Akan Disimpan:</span>
                                <div class="relative inline-block">
                                    <img :src="imagePreview" class="max-h-52 rounded-xl border-2 border-indigo-500 shadow-md object-contain bg-white">
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
                                        <input type="text" name="{{ $fieldName }}" value="{{ old($fieldName, $question->$fieldName) }}" required
                                            placeholder="Ketik teks jawaban pilihan {{ $opt }}"
                                            class="block w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
                                    </div>
                                    @error($fieldName)
                                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <!-- Penentuan Kunci Jawaban Benar -->
                        <div class="p-5 bg-indigo-50/60 rounded-2xl border border-indigo-100 space-y-3">
                            <div>
                                <label class="block text-xs font-extrabold text-indigo-950 uppercase tracking-wider">
                                    Tentukan Kunci Jawaban yang Benar <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-indigo-700/80 mt-0.5">
                                    Pilih opsi mana yang merupakan jawaban benar. Sistem akan menggunakannya untuk koreksi otomatis.
                                </p>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach(['A', 'B', 'C', 'D'] as $opt)
                                    <label class="relative flex items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition text-center"
                                        :class="selectedKey === '{{ $opt }}' 
                                            ? 'bg-emerald-600 text-white border-emerald-600 shadow-md scale-[1.02]' 
                                            : 'bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:bg-slate-50'">
                                        <input type="radio" name="correct_answer" value="{{ $opt }}" x-model="selectedKey" class="sr-only" required>
                                        <span class="font-black text-sm">Pilihan {{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @error('correct_answer')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                            <a href="{{ $exam ? route('guru.ujian.show', $exam->id) : route('guru.bank-soal.index') }}" 
                               class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                                Batal
                            </a>
                            <button type="submit" 
                                class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition hover:shadow-md">
                                Simpan Perubahan Soal
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
