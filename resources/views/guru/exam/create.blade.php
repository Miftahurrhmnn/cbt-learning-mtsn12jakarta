<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false, startTime: '{{ old('start_time', '') }}', endTime: '{{ old('end_time', '') }}' }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

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
                <!-- Page Breadcrumbs & Header -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Formulir Buat Ujian Baru</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi parameter ujian, kelas sasaran, serta jam pelaksanaan ujian sebelum menambahkan butir soal.</p>
                    </div>
                </div>

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

                        <!-- Mata Pelajaran -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="subject_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Mata Pelajaran <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">1 Guru dapat mengampu lebih dari 1 mata pelajaran</span>
                            </div>
                            <select name="subject_id" id="subject_id" required
                                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($subjects as $subject)
                                    @php
                                        $isDiampu = in_array($subject->id, $mySubjectIds ?? []);
                                    @endphp
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}{{ $isDiampu ? ' (Diampu)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-500 mt-1">
                                Anda dapat memilih mata pelajaran yang diampu maupun mapel lainnya. Mapel yang dipilih otomatis terhubung ke akun Anda.
                            </p>
                            @error('subject_id')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kelas Sasaran (Dapat Memilih Banyak Kelas) -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kelas Sasaran Ujian <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Centang kelas mana saja yang bisa mengakses ujian ini</span>
                            </div>

                            <!-- Penjelasan Terkait Hak Akses Kelas -->
                            <div class="mb-3 p-3.5 bg-yellow-300 border border-yellow-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                                <div class="w-8 h-8 rounded-xl bg-yellow-800 text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <!-- Mengubah text-white menjadi text-yellow-800 agar kontras dan mudah dibaca -->
                                <div class="text-xs text-black leading-relaxed">
                                    <strong class="font-extrabold block text-black">Penjelasan Hak Akses Kelas:</strong>
                                    Ujian ini <strong>hanya akan tampil dan dapat dikerjakan</strong> oleh siswa yang terdaftar pada kelas yang dicentang di bawah ini. Siswa dari kelas lain tidak akan dapat melihat maupun mengakses ujian ini di dashboard mereka.
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 p-3.5 bg-slate-50/80 rounded-2xl border border-slate-200">
                                @foreach($classrooms as $classroom)
                                    <label class="relative flex items-center gap-2.5 p-2.5 rounded-xl bg-white border border-slate-200 hover:border-indigo-400 cursor-pointer transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/60 shadow-2xs">
                                        <input type="checkbox" name="classroom_ids[]" value="{{ $classroom->id }}" 
                                            {{ (is_array(old('classroom_ids')) && in_array($classroom->id, old('classroom_ids'))) || old('classroom_id') == $classroom->id ? 'checked' : '' }}
                                            class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500"
                                        >
                                        <span class="text-xs font-semibold text-slate-800">{{ $classroom->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('classroom_ids')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                            @error('classroom_id')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jadwal Pelaksanaan (Tanggal Ujian - Hari Terdeteksi Otomatis) -->
                        <div x-data="{
                            selectedDate: '{{ old('exam_date') }}',
                            get detectedDay() {
                                if (!this.selectedDate) return '';
                                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                const parts = this.selectedDate.split('-');
                                if (parts.length === 3) {
                                    const d = new Date(parts[0], parts[1] - 1, parts[2]);
                                    return days[d.getDay()] || '';
                                }
                                return '';
                            }
                        }" class="space-y-2">
                            <label for="exam_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                                <span>Tanggal Pelaksanaan Ujian <span class="text-rose-500">*</span></span>
                                <template x-if="detectedDay">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200 normal-case shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Hari Otomatis: <span x-text="detectedDay"></span>
                                    </span>
                                </template>
                            </label>
                            <input type="date" name="exam_date" id="exam_date" x-model="selectedDate"
                                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white"
                                required
                                >
                            <p class="text-[11px] text-slate-400">
                                Cukup pilih tanggal ujian. Sistem akan mendeteksi dan mencatat hari pelaksanaan secara otomatis tanpa perlu input manual.
                            </p>
                            @error('exam_date')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Jam Pelaksanaan Ujian (WIB) - Menggantikan Input Durasi -->
                        <div class="p-5 bg-slate-50/90 rounded-2xl border border-slate-200/80 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                        Status Jam Pelaksanaan Ujian (WIB)
                                    </label>
                                    <p class="text-[11px] text-slate-500">Atur jam mulai akses dan batas jam selesai ujian yang berlaku bagi siswa.</p>
                                </div>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-500 border border-red-200 text-white text-xs font-bold">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-text="startTime && endTime ? (startTime + ' - ' + endTime + ' WIB') : (startTime ? ('Mulai ' + startTime + ' WIB') : (endTime ? ('Sampai ' + endTime + ' WIB') : 'Jam Belum Ditentukan'))"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_time" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Jam Mulai Ujian (WIB)
                                    </label>
                                    <input type="time" name="start_time" id="start_time" x-model="startTime"
                                        class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white"
                                        required
                                    >
                                    <p class="text-[11px] text-slate-400 mt-1">Jam mulai soal ujian dapat dibuka dan dikerjakan siswa.</p>
                                    @error('start_time')
                                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_time" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Jam Selesai Ujian (WIB)
                                    </label>
                                    <input type="time" name="end_time" id="end_time" x-model="endTime"
                                        class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs bg-white"
                                        required    
                                    >
                                    <p class="text-[11px] text-slate-400 mt-1">Batas akhir jam pengerjaan ujian bagi siswa.</p>
                                    @error('end_time')
                                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
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
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-400 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition hover:shadow-md">
                                Simpan & Lanjut Tambah Soal
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
