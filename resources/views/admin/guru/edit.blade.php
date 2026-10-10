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
                    <span class="text-xs font-bold text-slate-800">Edit Guru</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.guru.index') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600">
                        &larr; Data Guru
                    </a>
                </div>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition">Dashboard</a>
                    <span class="text-xs text-slate-300">/</span>
                    <a href="{{ route('admin.guru.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition">Data Guru</a>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">Edit Guru: {{ $teacher->name }}</span>
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
                        {{ __('Edit Data Guru') }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui profil, akun login, dan mata pelajaran yang diampu oleh {{ $teacher->name }}.</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <form method="POST" action="{{ route('admin.guru.update', $teacher->id) }}" class="p-6 sm:p-8 space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Nama Lengkap Guru -->
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Lengkap Guru <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $teacher->name) }}" required autofocus
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
                            @error('name')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Login -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Email / Akun Login <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $teacher->email) }}" required
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
                            @error('email')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Baru (Opsional) -->
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                                Ubah Password (Opsional)
                            </span>
                            <p class="text-[11px] text-slate-400 mb-3">Kosongkan kolom password jika Anda tidak ingin mengubah password guru ini.</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Password Baru
                                    </label>
                                    <input type="password" name="password" id="password"
                                           placeholder="Kosongkan jika tidak diubah"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
                                    @error('password')
                                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Ulangi Password Baru
                                    </label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           placeholder="Ketik ulang password baru"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan Mata Pelajaran yang Diampu (Multi-Select Checklist) -->
                        <div class="pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Mata Pelajaran yang Diampu
                                </label>
                                <span class="text-[11px] text-slate-400">Centang satu atau lebih mata pelajaran</span>
                            </div>

                            <div class="p-4 bg-slate-50/70 border border-slate-200/80 rounded-2xl space-y-2 max-h-60 overflow-y-auto">
                                @forelse($subjects as $sub)
                                    @php
                                        $isChecked = is_array(old('subject_ids')) 
                                            ? in_array($sub->id, old('subject_ids')) 
                                            : in_array($sub->id, $mySubjectIds ?? []);
                                    @endphp
                                    <label class="flex items-center gap-3 p-2.5 rounded-xl border {{ $isChecked ? 'border-indigo-400 bg-indigo-50/60' : 'border-slate-200 bg-white' }} hover:border-indigo-400 hover:bg-indigo-50/30 transition cursor-pointer">
                                        <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" 
                                               {{ $isChecked ? 'checked' : '' }}
                                               class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                                        <span class="text-xs sm:text-sm font-semibold text-slate-800 flex-1">{{ $sub->name }}</span>
                                        @if(in_array($sub->id, $mySubjectIds ?? []))
                                            <span class="text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">
                                                Sedang Diampu
                                            </span>
                                        @endif
                                    </label>
                                @empty
                                    <p class="text-xs text-slate-400 italic">Belum ada mata pelajaran terdaftar.</p>
                                @endforelse
                            </div>
                            @error('subject_ids')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                            @error('subject_ids.*')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('admin.guru.index') }}" 
                               class="px-5 py-2.5 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-blue-500/20 hover:shadow-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Perbarui Data Guru</span>
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>
</x-app-layout>
