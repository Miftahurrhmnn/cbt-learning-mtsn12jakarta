<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-rose-500 selection:text-white">

        <!-- Admin Sidebar Component (Desktop Sticky & Mobile Drawer) -->
        <x-admin-sidebar :teacher-count="$totalTeachers" />

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
                    <span class="text-xs font-bold text-slate-800">Data Guru</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.guru.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah</span>
                    </a>
                </div>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition">Dashboard</a>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">Master Data Guru</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

                <!-- Page Header Title & CTA Button -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="font-black text-2xl text-slate-900 leading-tight">
                            {{ __('Manajemen Data Guru') }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">Khusus Administrator. Kelola akun pengajar dan mata pelajaran yang diampu oleh masing-masing guru.</p>
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div id="success-alert" class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-xs transition-opacity duration-300">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="font-medium text-sm">{{ session('success') }}</span>
                        </div>
                        <button onclick="document.getElementById('success-alert').remove()" class="text-emerald-600 hover:text-emerald-800 p-1 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <span class="font-medium text-sm">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Filter & Search Bar -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs">
                    <form method="GET" action="{{ route('admin.guru.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <div class="sm:col-span-7">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau Email Guru..."
                                       class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <select name="subject_id" class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-white">
                                <option value="">-- Semua Mata Pelajaran --</option>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2 flex items-center gap-2">
                            <button type="submit" class="flex-1 py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span>Filter</span>
                            </button>
                            @if(request()->filled('search') || request()->filled('subject_id'))
                                <a href="{{ route('admin.guru.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs transition" title="Reset Filter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table Card Container -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Daftar Akun Pengajar</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Total {{ $teachers->total() }} guru terdaftar dalam sistem CBT</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                                    <th class="py-4 px-6 w-14 text-center">No</th>
                                    <th class="py-4 px-6">Nama Guru</th>
                                    <th class="py-4 px-6">Email Akun</th>
                                    <th class="py-4 px-6">Mata Pelajaran yang Diampu</th>
                                    <th class="py-4 px-6 text-center">Ujian Dibuat</th>
                                    <th class="py-4 px-6 text-center w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($teachers as $index => $teacher)
                                    @php
                                        $initials = strtoupper(mb_substr($teacher->name, 0, 2));
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-4 px-6 text-center text-slate-400 font-bold">
                                            {{ $teachers->firstItem() + $index }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 font-black flex items-center justify-center shrink-0 text-xs shadow-2xs">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <p class="font-extrabold text-slate-900 text-sm">{{ $teacher->name }}</p>
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 font-bold">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Guru Aktif
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-slate-600 font-medium">
                                            {{ $teacher->email }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex flex-wrap gap-1.5">
                                                @forelse($teacher->subjects as $sub)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                                        {{ $sub->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-[11px] text-slate-400 italic">Belum ditentukan</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                                {{ $teacher->createdExams->count() }} Paket
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.guru.edit', $teacher->id) }}" 
                                                   class="p-2 text-white rounded-lg bg-yellow-600 hover:bg-yellow-700 transition" 
                                                   title="Edit Guru">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>

                                                <!-- Delete Button Form -->
                                                <form action="{{ route('admin.guru.destroy', $teacher->id) }}" method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun guru {{ $teacher->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 rounded-lg bg-red-600 hover:bg-red-700 text-white transition" 
                                                            title="Hapus Guru">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-slate-400">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                            </div>
                                            <p class="font-bold text-slate-700 text-sm">Tidak Ada Data Guru Ditemukan</p>
                                            <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian lain atau tambahkan guru baru.</p>
                                            <div class="mt-4">
                                                <a href="{{ route('admin.guru.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">
                                                    <span>+ Tambah Guru Baru</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    @if($teachers->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $teachers->links() }}
                        </div>
                    @endif
                </div>

            </main>
        </div>
    </div>
</x-app-layout>
