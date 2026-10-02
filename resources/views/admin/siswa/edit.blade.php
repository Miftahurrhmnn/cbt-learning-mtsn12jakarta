<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1 text-xs text-slate-500">
                    <a href="{{ route('admin.siswa.index') }}" class="text-indigo-600 hover:text-indigo-800 font-bold">&larr; Kembali ke Daftar Siswa</a>
                    <span>/</span>
                    <span>Edit Data</span>
                </div>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">
                    Edit Data Siswa: {{ $student->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui data identitas siswa, kelas, atau ubah password.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <form method="POST" action="{{ route('admin.siswa.update', $student->id) }}" class="p-6 sm:p-8 space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Lengkap Siswa <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email & NISN Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Email / Akun Login <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $student->email) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                            @error('email')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NISN -->
                        <div>
                            <label for="nisn" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                NISN (Nomor Induk Siswa)
                            </label>
                            <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $student->nisn) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                            @error('nisn')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Kelas Sasaran -->
                    <div>
                        <label for="classroom_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kelas Siswa
                        </label>
                        <select name="classroom_id" id="classroom_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-2xs bg-white">
                            <option value="">-- Pilih Kelas Siswa --</option>
                            @foreach($classrooms as $cls)
                                <option value="{{ $cls->id }}" {{ old('classroom_id', $student->classroom_id) == $cls->id ? 'selected' : '' }}>
                                    🏫 {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('classroom_id')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Baru (Opsional) -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Ganti Password (Opsional)</span>
                            <p class="text-[11px] text-slate-400">Biarkan kosong jika tidak ingin mengubah password siswa.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-semibold text-slate-600 mb-1">Password Baru</label>
                                <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak diubah"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                                @error('password')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.siswa.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-indigo-100 transition hover:-translate-y-0.5">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
