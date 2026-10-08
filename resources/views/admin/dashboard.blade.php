<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-rose-500 selection:text-white">

        <!-- Admin Sidebar Component (Desktop Sticky & Mobile Drawer) -->
        <x-admin-sidebar :student-count="$totalStudents" />

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
                    <span class="text-xs font-bold text-slate-800">Dashboard Admin</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                        AD
                    </span>
                </div>
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

            <!-- Main Scrollable Body Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

                <!-- Alert Notifications -->
                @if(session('success'))
                    <div id="success-alert" class="flex items-center justify-between gap-3 rounded-2xl border border-emerald-200/80 bg-emerald-50/90 p-4 text-emerald-800 shadow-xs transition-opacity duration-300">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-xs">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-wider text-emerald-950">Berhasil</p>
                                <p class="text-sm font-medium mt-0.5">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button onclick="document.getElementById('success-alert').remove()" class="text-emerald-600 hover:text-emerald-800 p-1.5 rounded-lg transition" aria-label="Tutup Notifikasi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <!-- Hero Welcome Banner -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10">
                    <div class="absolute -top-12 -right-12 w-56 h-56 rounded-full bg-rose-500/10 pointer-events-none blur-2xl"></div>
                    <div class="absolute bottom-0 right-1/4 w-40 h-40 rounded-full bg-indigo-500/10 pointer-events-none blur-xl"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-2 max-w-2xl">
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight leading-snug">
                                Selamat Datang, {{ Auth::user()->name }}
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                                Pusat kendali administrasi CBT MTsN 12 Jakarta. Kelola akun siswa, pantau ketersediaan kelas, dan pastikan seluruh peserta terdata dengan valid.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4 Statistics Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    
                    <!-- Card 1: Total Siswa -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-2xs hover:shadow-md transition group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <span class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                                Siswa
                            </span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa</p>
                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">{{ $totalStudents }}</h3>
                    </div>

                    <!-- Card 2: Total Rombel / Kelas -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-2xs hover:shadow-md transition group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                Kelas
                            </span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kelas</p>
                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">{{ $totalClassrooms }}</h3>
                    </div>

                    <!-- Card 3: Total Guru -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-2xs hover:shadow-md transition group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                            <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">
                                Guru
                            </span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Guru</p>
                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">{{ $totalTeachers }}</h3>
                    </div>

                    <!-- Card 4: Total Ujian -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-2xs hover:shadow-md transition group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-105 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </div>
                            <span class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full">
                                Paket
                            </span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Paket Ujian</p>
                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">{{ $totalExams }}</h3>
                    </div>

                </div>

                <!-- 2-Column Content Grid: Siswa Baru & Distribusi Rombel -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Kolom 1 (2/3): Siswa Terdaftar Terbaru -->
                    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <h3 class="font-black text-base text-slate-900 leading-tight">Siswa Terdaftar Terbaru</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">5 siswa terakhir yang ditambahkan ke sistem</p>
                                </div>
                                <a href="{{ route('admin.siswa.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition">
                                    Lihat Semua &rarr;
                                </a>
                            </div>

                            @if($recentStudents->isEmpty())
                                <div class="p-12 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">Belum Ada Data Siswa</p>
                                    <p class="text-xs text-slate-400 mt-1">Mulai tambahkan siswa baru untuk mengaktifkan ujian.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.siswa.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-700 transition">
                                            + Tambah Siswa Sekarang
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100">
                                            <tr>
                                                <th class="px-5 py-3.5">Nama & Email</th>
                                                <th class="px-5 py-3.5">NISN</th>
                                                <th class="px-5 py-3.5">Kelas</th>
                                                <th class="px-5 py-3.5">Terdaftar</th>
                                                <th class="px-5 py-3.5 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-700">
                                            @foreach($recentStudents as $student)
                                                <tr class="hover:bg-slate-50/60 transition">
                                                    <td class="px-5 py-3.5">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0">
                                                                {{ strtoupper(substr($student->name, 0, 2)) }}
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="font-bold text-slate-900 truncate">{{ $student->name }}</p>
                                                                <p class="text-[11px] text-slate-400 font-mono truncate">{{ $student->email }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-3.5 font-mono font-bold text-slate-700">
                                                        {{ $student->nisn ?? '-' }}
                                                    </td>
                                                    <td class="px-5 py-3.5">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700">
                                                            {{ $student->classroom->name ?? 'Belum Ada' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-5 py-3.5 text-slate-400 text-[11px]">
                                                        {{ $student->created_at ? $student->created_at->format('d M Y') : '-' }}
                                                    </td>
                                                    <td class="px-5 py-3.5 text-right">
                                                        <a href="{{ route('admin.siswa.edit', $student->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition inline-block" title="Edit Data">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Footer Link Card -->
                        <div class="p-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-medium">Total {{ $totalStudents }} siswa di database</span>
                        </div>
                    </div>

                    <!-- Kolom 2 (1/3): Rombel & Informasi Sistem -->
                    <div class="space-y-6">

                        <!-- Distribusi Rombel -->
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs p-5 sm:p-6 space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div>
                                    <h3 class="font-black text-sm text-slate-900 leading-tight">Distribusi Rombel Kelas</h3>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Jumlah siswa aktif tiap kelas</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                    {{ $totalClassrooms }} Kelas
                                </span>
                            </div>

                            <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                                @forelse($classrooms as $classroom)
                                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100/70 border border-slate-100 transition">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shadow-2xs">
                                                {{ substr($classroom->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs font-bold text-slate-800">{{ $classroom->name }}</span>
                                        </div>
                                        <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">
                                            {{ $classroom->students_count }} Siswa
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 text-center py-4">Belum ada kelas.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- System Security Card -->
                        <div class="p-5 rounded-3xl bg-gradient-to-br from-rose-500/10 via-rose-50 to-white border border-rose-100 text-xs space-y-2.5 shadow-2xs">
                            <div class="flex items-center gap-2 text-rose-800 font-bold">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Izin Khusus Administrator</span>
                            </div>
                            <p class="text-rose-950/80 leading-relaxed text-[11px]">
                                Pengelolaan akun siswa dan hak akses login hanya dapat dilakukan melalui portal admin ini. Pastikan NISN dan email siswa unik dan terverifikasi.
                            </p>
                        </div>

                    </div>

                </div>

            </main>
        </div>
    </div>
</x-app-layout>
