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
                    <span class="text-xs font-bold text-slate-800">Tambah Siswa</span>
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
                    <span class="text-xs font-bold text-slate-700">Tambah Baru</span>
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
                    <h1 class="font-black text-2xl text-slate-900 leading-tight">
                        {{ __('Input Data Siswa Baru') }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">Akun siswa akan disimpan ke database dan langsung dapat login untuk mengerjakan ujian.</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <form method="POST" action="{{ route('admin.siswa.store') }}" class="p-6 sm:p-8 space-y-5">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Lengkap Siswa <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                   placeholder="Contoh: Muhammad Raihan Pratama"
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
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       placeholder="siswa@sekolah.sch.id"
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
                                <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}"
                                       placeholder="Contoh: 0081234567"
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
                                    <option value="{{ $cls->id }}" {{ old('classroom_id') == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classroom_id')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                            <div>
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password" id="password" required
                                       placeholder="Minimal 8 karakter"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
                                @error('password')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Konfirmasi Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       placeholder="Ulangi password di atas"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <a href="{{ route('admin.siswa.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-indigo-100 transition active:scale-[0.98]">
                                Simpan Data Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
