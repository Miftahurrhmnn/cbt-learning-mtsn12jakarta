<x-app-layout :hide-nav="true">
    <div x-data="attendanceManager()" x-init="init()" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

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
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-slate-800 text-sm">Daftar Hadir</span>
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live
                        </span>
                    </div>
                </div>
                <a href="{{ route('guru.ujian.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
                    Daftar Ujian
                </a>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Portal Guru</span>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('guru.ujian.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition">Manajemen Ujian</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">Daftar Hadir Siswa Realtime</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100/80 border border-slate-200/60 text-xs font-semibold text-slate-600">
                        <span class="w-2 h-2 rounded-full" :class="isPolling ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                        <span x-text="isPolling ? 'Realtime Aktif' : 'Realtime Jeda'">Realtime Aktif</span>
                        <span class="text-[11px] text-slate-400 font-mono" x-text="'(' + lastUpdated + ')'"></span>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">

                <!-- Page Header Hero Title & Action Controls -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Hadir Siswa Masuk Ujian</h1>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Pantau kehadiran dan jam masuk siswa ke dalam ruang ujian Anda secara langsung dan otomatis.
                        </p>
                    </div>

                    <!-- Right Controls: Toggle Auto Refresh & Manual Refresh Button -->
                    <div class="flex items-center gap-2.5">
                        <button type="button" @click="toggleAutoRefresh()" 
                                :class="autoRefresh ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-white border-slate-200 text-slate-600'"
                                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl border text-xs font-bold transition shadow-2xs hover:bg-slate-50">
                            <span class="relative flex h-2 w-2">
                                <span x-show="autoRefresh" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2" :class="autoRefresh ? 'bg-indigo-600' : 'bg-slate-400'"></span>
                            </span>
                            <span x-text="autoRefresh ? 'Auto Refresh (5s): ON' : 'Auto Refresh: OFF'">Auto Refresh (5s): ON</span>
                        </button>

                        <button type="button" @click="fetchAttendanceData(true)" 
                                :disabled="isLoading"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:opacity-50 text-white rounded-2xl text-xs font-bold shadow-md shadow-blue-500/20 transition cursor-pointer">
                            <svg class="w-4 h-4" :class="{ 'animate-spin': isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="isLoading ? 'Memuat...' : 'Segarkan'">Segarkan</span>
                        </button>
                    </div>
                </div>

                <!-- 4 Stat Summary Cards (Realtime Reactive) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total Siswa Terdaftar -->
                    <div class="p-5 bg-white rounded-3xl border border-slate-200/80 shadow-2xs flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Siswa Sasaran</span>
                            <p class="text-2xl font-black text-slate-900 mt-0.5" x-text="stats.total_target">{{ $totalTargetStudents }}</p>
                        </div>
                    </div>

                    <!-- Hadir (Masuk Ujian) -->
                    <div class="p-5 bg-emerald-600 rounded-3xl border border-emerald-500 shadow-2xs flex items-center gap-4 text-white">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-100">Siswa Hadir</span>
                            <p class="text-2xl font-black text-white mt-0.5" x-text="stats.total_hadir">{{ $totalHadir }}</p>
                        </div>
                    </div>

                    <!-- Belum Hadir -->
                    <div class="p-5 bg-amber-500 rounded-3xl border border-amber-400 shadow-2xs flex items-center gap-4 text-white">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-100">Belum Hadir</span>
                            <p class="text-2xl font-black text-white mt-0.5" x-text="stats.total_belum_hadir">{{ $totalBelumHadir }}</p>
                        </div>
                    </div>

                    <!-- Persentase Kehadiran -->
                    <div class="p-5 bg-slate-900 rounded-3xl border border-slate-800 shadow-2xs flex items-center gap-4 text-white">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tingkat Kehadiran</span>
                            <p class="text-2xl font-black text-emerald-400 mt-0.5" x-text="stats.percent + '%'">{{ $attendancePercent }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search Toolbar -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-4">
                    <form method="GET" action="{{ route('guru.attendance.index') }}" id="filterForm" @submit="onFormSubmit($event)" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <input type="hidden" name="page" :value="filters.page">
                        
                        <!-- Filter Pilih Ujian -->
                        <div class="sm:col-span-3">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pilih Ujian Anda</label>
                            <select name="exam_id" x-model="filters.exam_id" @change="onFilterChange()"
                                    class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-slate-50/50">
                                <option value="">-- Semua Ujian Guru --</option>
                                @foreach($exams as $ex)
                                    <option value="{{ $ex->id }}" {{ $selectedExamId == $ex->id ? 'selected' : '' }}>
                                        {{ $ex->title }} ({{ $ex->subject->name ?? 'Mapel' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Kelas Siswa -->
                        <div class="sm:col-span-3">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Filter Kelas</label>
                            <select name="classroom_id" x-model="filters.classroom_id" @change="onFilterChange()"
                                    class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-slate-50/50">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($classrooms as $cls)
                                    <option value="{{ $cls->id }}" {{ $selectedClassroomId == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Status Hadir / Belum Hadir -->
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status Hadir</label>
                            <select name="status" x-model="filters.status" @change="onFilterChange()"
                                    class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-slate-50/50">
                                <option value="">Semua</option>
                                <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="belum_hadir" {{ request('status') === 'belum_hadir' ? 'selected' : '' }}>Belum Hadir</option>
                            </select>
                        </div>

                        <!-- Pencarian Nama / NISN -->
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Cari Siswa</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" name="search" x-model="filters.search" @input.debounce.400ms="onFilterChange()"
                                       value="{{ request('search') }}" placeholder="Ketik nama atau NISN..."
                                       class="w-full pl-10 pr-3 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            </div>
                        </div>

                        <!-- Pilihan Jumlah Baris Per Halaman -->
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Per Halaman</label>
                            <select name="per_page" x-model="filters.per_page" @change="onFilterChange()"
                                    class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-slate-50/50">
                                <option value="10">10 Baris</option>
                                <option value="15">15 Baris</option>
                                <option value="25">25 Baris</option>
                                <option value="50">50 Baris</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- TABLE DAFTAR HADIR SISWA REALTIME -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-sm sm:text-base text-slate-900">
                                Data Kehadiran Masuk Ruang Ujian
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700" x-text="'Hal ' + pagination.current_page + ' dari ' + pagination.last_page + ' (' + pagination.total + ' Siswa)'">
                                Hal {{ $pagination['current_page'] }} dari {{ $pagination['last_page'] }} ({{ $pagination['total'] }} Siswa)
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Auto-update tiap 5 detik</span>
                        </div>
                    </div>

                    <!-- Tabel Container Responsive -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                    <th class="py-3.5 px-4 text-center w-16">No</th>
                                    <th class="py-3.5 px-4">Nama Siswa</th>
                                    <th class="py-3.5 px-4">NISN</th>
                                    <th class="py-3.5 px-4">Kelas</th>
                                    <th class="py-3.5 px-4">Nama Ujian</th>
                                    <th class="py-3.5 px-4 text-center">Jam Masuk</th>
                                    <th class="py-3.5 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700" id="attendanceTableBody">
                                <!-- Render Awal Blade (No JS / Fallback) -->
                                @forelse($records as $rec)
                                    <tr class="hover:bg-slate-50/80 transition" id="row-{{ $rec['row_key'] ?? $rec['student_id'] }}">
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                            {{ $rec['no'] }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold flex items-center justify-center shrink-0 text-[11px]">
                                                    {{ strtoupper(substr($rec['name'], 0, 2)) }}
                                                </div>
                                                <span class="font-extrabold text-slate-900">{{ $rec['name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-600">
                                            {{ $rec['nisn'] }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                                                {{ $rec['classroom'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                                            {{ $rec['exam_title'] }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($rec['is_hadir'])
                                                <span class="inline-flex items-center gap-1.5 font-mono font-bold text-slate-800 bg-slate-100/90 px-2.5 py-1 rounded-xl">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ $rec['jam_masuk'] }} WIB
                                                </span>
                                            @else
                                                <span class="text-slate-400 font-medium italic">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($rec['is_hadir'])
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Hadir
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Belum Hadir
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyRow">
                                        <td colspan="7" class="py-12 text-center">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            </div>
                                            <p class="font-bold text-sm text-slate-800">Tidak ada data kehadiran ditemukan</p>
                                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian atau pilih ujian yang berbeda.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Controls (Responsive & Realtime Seamless) -->
                    <div class="px-6 py-4 border-t border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Informasi Baris Data -->
                        <div class="text-xs text-slate-500 font-medium">
                            Menampilkan <span class="font-bold text-slate-900" x-text="pagination.from">{{ $pagination['from'] }}</span> sampai <span class="font-bold text-slate-900" x-text="pagination.to">{{ $pagination['to'] }}</span> dari <span class="font-bold text-slate-900" x-text="pagination.total">{{ $pagination['total'] }}</span> siswa sasaran
                        </div>

                        <!-- Tombol Paginasi -->
                        <div class="flex items-center gap-1.5" x-show="pagination.last_page > 1">
                            <!-- Tombol Previous -->
                            <button type="button" @click="goToPage(pagination.current_page - 1)"
                                    :disabled="pagination.current_page <= 1"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition shadow-2xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                <span class="hidden sm:inline">Sebelumnya</span>
                            </button>

                            <!-- Nomor-nomor Halaman Pintar -->
                            <div class="flex items-center gap-1">
                                <template x-for="p in getPageNumbers()" :key="p">
                                    <div>
                                        <template x-if="p === '...'">
                                            <span class="w-8 h-8 flex items-center justify-center text-slate-400 text-xs font-bold">...</span>
                                        </template>
                                        <template x-if="p !== '...'">
                                            <button type="button" @click="goToPage(p)"
                                                    :class="pagination.current_page === p ? 'bg-blue-600 text-white font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100 font-bold'"
                                                    class="w-8 h-8 rounded-xl text-xs flex items-center justify-center transition cursor-pointer"
                                                    x-text="p">
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Next -->
                            <button type="button" @click="goToPage(pagination.current_page + 1)"
                                    :disabled="pagination.current_page >= pagination.last_page"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition shadow-2xs cursor-pointer">
                                <span class="hidden sm:inline">Berikutnya</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Alpine.js Realtime Controller & AJAX Polling Script -->
    <script>
        function attendanceManager() {
            return {
                autoRefresh: true,
                isPolling: true,
                isLoading: false,
                lastUpdated: '{{ now()->format("H:i:s") }}',
                pollingInterval: null,
                filters: {
                    exam_id: '{{ $selectedExamId ?? "" }}',
                    classroom_id: '{{ $selectedClassroomId ?? "" }}',
                    status: '{{ request("status") ?? "" }}',
                    search: '{{ request("search") ?? "" }}',
                    page: {{ $pagination['current_page'] ?? 1 }},
                    per_page: {{ $pagination['per_page'] ?? 15 }}
                },
                pagination: @json($pagination),
                stats: {
                    total_target: {{ $totalTargetStudents }},
                    total_hadir: {{ $totalHadir }},
                    total_belum_hadir: {{ $totalBelumHadir }},
                    percent: {{ $attendancePercent }}
                },
                records: @json($records),

                init() {
                    // Mulai polling otomatis setiap 5 detik
                    this.startPolling();

                    // Bersihkan interval jika berpindah halaman
                    window.addEventListener('beforeunload', () => {
                        this.stopPolling();
                    });
                },

                toggleAutoRefresh() {
                    this.autoRefresh = !this.autoRefresh;
                    if (this.autoRefresh) {
                        this.startPolling();
                    } else {
                        this.stopPolling();
                    }
                },

                startPolling() {
                    this.stopPolling();
                    this.isPolling = true;
                    this.pollingInterval = setInterval(() => {
                        if (this.autoRefresh && !this.isLoading) {
                            this.fetchAttendanceData(false);
                        }
                    }, 5000);
                },

                stopPolling() {
                    this.isPolling = false;
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                        this.pollingInterval = null;
                    }
                },

                onFilterChange() {
                    this.filters.page = 1;
                    this.fetchAttendanceData(false);
                },

                onFormSubmit(e) {
                    e.preventDefault();
                    this.filters.page = 1;
                    this.fetchAttendanceData(true);
                },

                goToPage(p) {
                    if (p < 1 || p > this.pagination.last_page || p === this.pagination.current_page) return;
                    this.filters.page = p;
                    this.fetchAttendanceData(true);
                },

                getPageNumbers() {
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    if (last <= 7) {
                        const pages = [];
                        for (let i = 1; i <= last; i++) pages.push(i);
                        return pages;
                    }
                    const pages = [1];
                    if (current > 3) {
                        pages.push('...');
                    }
                    const start = Math.max(2, current - 1);
                    const end = Math.min(last - 1, current + 1);
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    if (current < last - 2) {
                        pages.push('...');
                    }
                    pages.push(last);
                    return pages;
                },

                async fetchAttendanceData(manual = false) {
                    if (this.isLoading) return;
                    if (manual) this.isLoading = true;

                    const params = new URLSearchParams({
                        exam_id: this.filters.exam_id,
                        classroom_id: this.filters.classroom_id,
                        status: this.filters.status,
                        search: this.filters.search,
                        page: this.filters.page,
                        per_page: this.filters.per_page
                    });

                    try {
                        const response = await fetch(`{{ route('guru.attendance.data') }}?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const result = await response.json();
                            if (result.success) {
                                this.stats = result.stats;
                                this.records = result.rows;
                                this.pagination = result.pagination;
                                this.lastUpdated = result.updated_at;
                                this.renderTable(result.rows);

                                // Update URL query string secara dinamis tanpa reload halaman
                                const newUrl = `${window.location.pathname}?${params.toString()}`;
                                window.history.replaceState({ path: newUrl }, '', newUrl);
                            }
                        }
                    } catch (error) {
                        console.error('Gagal memperbarui data kehadiran realtime:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                renderTable(rows) {
                    const tbody = document.getElementById('attendanceTableBody');
                    if (!tbody) return;

                    if (!rows || rows.length === 0) {
                        tbody.innerHTML = `
                            <tr id="emptyRow">
                                <td colspan="7" class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="font-bold text-sm text-slate-800">Tidak ada data kehadiran ditemukan</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian atau pilih ujian yang berbeda.</p>
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    let html = '';
                    rows.forEach((rec) => {
                        const initials = (rec.name || 'SS').substring(0, 2).toUpperCase();
                        const jamMasukHtml = rec.is_hadir 
                            ? `<span class="inline-flex items-center gap-1.5 font-mono font-bold text-slate-800 bg-slate-100/90 px-2.5 py-1 rounded-xl">
                                   <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                   ${rec.jam_masuk} WIB
                               </span>`
                            : `<span class="text-slate-400 font-medium italic">-</span>`;

                        const statusHtml = rec.is_hadir
                            ? `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                   <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                   Hadir
                               </span>`
                            : `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                   <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                   Belum Hadir
                               </span>`;

                        html += `
                            <tr class="hover:bg-slate-50/80 transition" id="row-${rec.row_key || rec.student_id}">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                    ${rec.no}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold flex items-center justify-center shrink-0 text-[11px]">
                                            ${initials}
                                        </div>
                                        <span class="font-extrabold text-slate-900">${rec.name}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-600">
                                    ${rec.nisn}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                                        ${rec.classroom}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    ${rec.exam_title}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    ${jamMasukHtml}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    ${statusHtml}
                                </td>
                            </tr>
                        `;
                    });

                    tbody.innerHTML = html;
                }
            };
        }
    </script>
</x-app-layout>
