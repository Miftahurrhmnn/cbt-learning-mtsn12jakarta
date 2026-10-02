<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8" x-data="{ duration: {{ old('duration', 60) }} }">
            <div class="bg-white overflow-hidden shadow-xs rounded-3xl border border-slate-200/80">
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
        </div>
    </div>
</x-app-layout>
