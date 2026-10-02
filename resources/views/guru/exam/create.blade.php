<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false, duration: {{ old('duration', 60) }} }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

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
                    <span class="font-extrabold text-slate-800 text-sm">Buat Ujian Baru</span>
                </div>
                <a href="{{ route('guru.ujian.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar Ujian</span>
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
                <!-- Form Card -->
                <div class="bg-white overflow-hidden shadow-2xs rounded-3xl border border-slate-200/80">
                    <form action="{{ route('guru.ujian.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                        @csrf

                        <!-- Judul Ujian -->
                        <div>
                            <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Judul Ujian <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Contoh: Penilaian Harian Matematika Aljabar" required
                                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
                            @error('title')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Mata Pelajaran -->
                            <div>
                                <label for="subject_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Mata Pelajaran <span class="text-rose-500">*</span>
                                </label>
                                <select name="subject_id" id="subject_id" required
                                    class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            <x-heroicon-s-book-open class="h-5 w-5" /> {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kelas Sasaran -->
                            <div>
                                <label for="classroom_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Kelas Sasaran <span class="text-rose-500">*</span>
                                </label>
                                <select name="classroom_id" id="classroom_id" required
                                    class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                            🏫 {{ $classroom->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('classroom_id')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Jadwal Pelaksanaan (Hari & Tanggal Ujian) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Hari Pelaksanaan -->
                            <div>
                                <label for="day_of_week" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Hari Pelaksanaan (Jadwal Mapel)
                                </label>
                                <select name="day_of_week" id="day_of_week"
                                    class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white">
                                    <option value="">-- Pilih Hari Ujian (Opsional) --</option>
                                    @foreach($daysList as $dayName)
                                        <option value="{{ $dayName }}" {{ old('day_of_week') == $dayName ? 'selected' : '' }}>
                                            📅 {{ $dayName }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk memfilter jadwal ujian siswa dan guru.</p>
                                @error('day_of_week')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Ujian -->
                            <div>
                                <label for="exam_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Pelaksanaan (Opsional)
                                </label>
                                <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date') }}"
                                    class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white">
                                <p class="text-[11px] text-slate-400 mt-1">Jika hari kosong, sistem otomatis menghitung hari dari tanggal ini.</p>
                                @error('exam_date')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Durasi Pengerjaan & Preset Tombol Cepat -->
                        <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <label for="duration" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Durasi Ujian (Menit) <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-xs font-bold text-indigo-600" x-text="duration + ' Menit (' + (duration / 60) + ' Jam)'"></span>
                            </div>

                            <!-- Presets Cepat -->
                            <div class="grid grid-cols-4 gap-2">
                                <button type="button" @click="duration = 30"
                                    class="py-1.5 text-xs font-bold rounded-lg border transition text-center"
                                    :class="duration === 30 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                    30 Menit
                                </button>
                                <button type="button" @click="duration = 60"
                                    class="py-1.5 text-xs font-bold rounded-lg border transition text-center"
                                    :class="duration === 60 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                    60 Menit (1 Jam)
                                </button>
                                <button type="button" @click="duration = 90"
                                    class="py-1.5 text-xs font-bold rounded-lg border transition text-center"
                                    :class="duration === 90 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                    90 Menit
                                </button>
                                <button type="button" @click="duration = 120"
                                    class="py-1.5 text-xs font-bold rounded-lg border transition text-center"
                                    :class="duration === 120 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                    120 Menit (2 Jam)
                                </button>
                            </div>

                            <input type="number" name="duration" id="duration" x-model="duration" min="5" max="300" required
                                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-white shadow-2xs">
                            @error('duration')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="token" class="block text-sm font-semibold text-slate-700">
                                Token Ujian
                            </label>

                            <p class="mt-1 text-xs text-slate-500">
                                Buat token sendiri dengan tepat 7 karakter. Gunakan kombinasi huruf dan angka.
                            </p>

                            <input
                                id="token"
                                name="token"
                                type="text"
                                value="{{ old('token') }}"
                                maxlength="7"
                                minlength="7"
                                required
                                autocomplete="off"
                                class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Contoh: MTK2026"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 7)"
                            >

                            @error('token')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Status Awal Ujian -->
                        <div>
                            <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Status Awal Ujian <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>
                                    <x-heroicon-s-lock-closed class="h-4 w-4" /> 
                                    Draft (Simpan dulu, belum dibuka untuk siswa)
                                </option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>
                                    <x-heroicon-o-signal class="h-5 w-5 text-emerald-600" />
                                     Published (Langsung dibuka untuk siswa)
                                </option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Anda dapat membuka atau menutup ujian kapan saja setelah soal ditambahkan.</p>
                            @error('status')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('guru.ujian.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition hover:shadow-md">
                                Simpan & Lanjut Tambah Soal
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
