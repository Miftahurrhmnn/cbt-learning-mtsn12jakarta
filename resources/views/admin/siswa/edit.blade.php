<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-rose-500 selection:text-white">

        <!-- Admin Sidebar Component (Desktop Sticky & Mobile Drawer) -->
        <x-admin-sidebar />

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
                    <span class="text-xs font-bold text-slate-800">Edit Siswa</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                        AD
                    </span>
                </div>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition">Dashboard</a>
                    <span class="text-xs text-slate-300">/</span>
                    <a href="{{ route('admin.siswa.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition">Data Siswa</a>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">Edit Data</span>
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

                <!-- Page Header Title -->
                <div>
                    <div class="flex items-center gap-2 mb-1 text-xs text-slate-500">
                        <a href="{{ route('admin.siswa.index') }}" class="text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali ke Daftar Siswa
                        </a>
                    </div>
                    <h1 class="font-black text-2xl text-slate-900 leading-tight">
                        Edit Data Siswa: <span class="text-indigo-600">{{ $student->name }}</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui data identitas siswa, kelas, atau ubah password akun.</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <form method="POST" action="{{ route('admin.siswa.update', $student->id) }}" class="p-6 sm:p-8 space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Lengkap Siswa <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" required
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
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
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
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
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
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
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs bg-white">
                                <option value="">-- Pilih Kelas Siswa --</option>
                                @foreach($classrooms as $cls)
                                    <option value="{{ $cls->id }}" {{ old('classroom_id', $student->classroom_id) == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->name }}
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
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
                                    @error('password')
                                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <a href="{{ route('admin.siswa.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-indigo-100 transition hover:-translate-y-0.5 active:scale-[0.98]">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
