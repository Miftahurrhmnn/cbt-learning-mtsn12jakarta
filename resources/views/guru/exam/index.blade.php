<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

        <!-- Guru Sidebar Component (Desktop Sticky & Mobile Drawer) -->
        <x-guru-sidebar :exam-count="$exams->total()" />


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
                    <div id="success-alert" class="flex items-center justify-between gap-3 rounded-2xl border border-emerald-200/80 bg-emerald-50/90 p-4 text-emerald-800 shadow-sm transition-opacity duration-300">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-xs">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
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

                    <script>
                        setTimeout(function() {
                            var el = document.getElementById('success-alert');
                            if (el) {
                                el.style.opacity = '0';
                                setTimeout(function() { el.remove(); }, 300);
                            }
                        }, 5000);
                    </script>
                @endif

                @if(session('error'))
                    <div id="error-alert" class="flex items-center justify-between gap-3 rounded-2xl border border-rose-200/80 bg-rose-50/90 p-4 text-rose-800 shadow-sm transition-opacity duration-300">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white shadow-xs">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-wider text-rose-950">Perhatian</p>
                                <p class="text-sm font-medium mt-0.5">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button onclick="document.getElementById('error-alert').remove()" class="text-rose-600 hover:text-rose-800 p-1.5 rounded-lg transition" aria-label="Tutup Notifikasi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <!-- Page Header Hero Title -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen & Daftar Ujian</h1>
                    </div>
                </div>

                <!-- 3 Stat Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                    <!-- Total Ujian -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs transition hover:shadow-md hover:border-indigo-200">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-inner">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Ujian Dibuat</span>
                                <h4 class="mt-0.5 text-2xl font-black text-slate-900">{{ $exams->total() }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Ujian Aktif (Published) -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs transition hover:shadow-md hover:border-emerald-200">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 shadow-inner">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Ujian Aktif (Published)</span>
                                <h4 class="mt-0.5 text-2xl font-black text-emerald-600">{{ $exams->where('status', 'published')->count() }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Total Partisipasi Siswa -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs transition hover:shadow-md hover:border-purple-200">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 shadow-inner">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Partisipasi Siswa</span>
                                <h4 class="mt-0.5 text-2xl font-black text-purple-700">{{ $exams->sum('sessions_count') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exam Content Card (Responsive Table on Desktop & Cards on Mobile) -->
                <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-2xs">
                    
                    <!-- Table Card Header & Filters -->
                    <div class="p-6 border-b border-slate-100 bg-slate-50/40 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 uppercase">Daftar Pelaksanaan Ujian</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Filter berdasarkan kelas atau hari pelaksanaan ujian.</p>
                            </div>
                            <span class="text-xs font-bold text-slate-500 font-mono">
                                Total: {{ $exams->total() }} Ujian
                            </span>
                        </div>

                        <!-- Form Filter Guru (Kelas, Hari, Status, Search) -->
                        <form method="GET" action="{{ route('guru.ujian.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2">
                            <!-- Filter Pencarian -->
                            <div class="sm:col-span-4">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul ujian / mapel..."
                                           class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                                </div>
                            </div>

                            <!-- Filter Kelas untuk Guru -->
                            <div class="sm:col-span-3">
                                <select name="classroom_id" onchange="this.form.submit()"
                                        class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                                    <option value="">Semua Kelas</option>
                                    @foreach($classrooms as $cls)
                                        <option value="{{ $cls->id }}" {{ request('classroom_id') == $cls->id ? 'selected' : '' }}>
                                            🏫 {{ $cls->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Hari untuk Guru -->
                            <div class="sm:col-span-3">
                                <select name="day" onchange="this.form.submit()"
                                        class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white">
                                    <option value="">Semua Hari</option>
                                    @foreach($daysList as $dayName)
                                        <option value="{{ $dayName }}" {{ request('day') == $dayName ? 'selected' : '' }}>
                                            📅 Hari {{ $dayName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tombol Reset / Submit -->
                            <div class="sm:col-span-2 flex items-center gap-1.5">
                                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-xs">
                                    Filter
                                </button>
                                @if(request()->hasAny(['search', 'classroom_id', 'day', 'status']))
                                    <a href="{{ route('guru.ujian.index') }}" title="Reset Filter"
                                       class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    @if($exams->isEmpty())
                        <!-- Empty State -->
                        <div class="py-16 px-4 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500 shadow-inner">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Belum ada ujian yang dibuat</h3>
                            <p class="mx-auto mt-1 max-w-sm text-xs leading-relaxed text-slate-500">Mulai dengan memilih mata pelajaran dan kelas sasaran untuk menyiapkan paket ujian pertama.</p>
                            <div class="mt-6">
                                <a href="{{ route('guru.ujian.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-indigo-100 transition hover:bg-indigo-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Buat Ujian Sekarang</span>
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- ==================== DESKTOP TABLE VIEW (md+) ==================== -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead>
                                    <tr class="bg-slate-50/70">
                                        <th scope="col" class="px-6 py-3.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Judul</th>
                                        <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Kelas</th>
                                        <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Durasi</th>
                                        <th scope="col" class="px-5 py-3.5 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Soal</th>
                                        <th scope="col" class="px-5 py-3.5 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Status Akses</th>
                                        <th scope="col" class="px-5 py-3.5 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Token</th>
                                        <th scope="col" class="px-6 py-3.5 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Aksi Guru</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($exams as $exam)
                                        <tr class="transition hover:bg-slate-50/70 group">
                                            <!-- Judul & Mapel -->
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition">
                                                    {{ $exam->title ?? 'Ujian ' . ($exam->subject->name ?? '-') }}
                                                </div>
                                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs font-semibold">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px]">
                                                        Mapel: {{ $exam->subject->name ?? '-' }}
                                                    </span>
                                                    @if($exam->day_of_week)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[11px] font-bold">
                                                            📅 {{ $exam->day_of_week }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Kelas -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black">
                                                    {{ $exam->classroom->name ?? '-' }}
                                                </span>
                                            </td>

                                            <!-- Durasi -->
                                            <td class="px-5 py-4 whitespace-nowrap text-xs font-medium text-slate-600">
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span class="font-bold text-slate-700">{{ $exam->duration }}</span> Menit
                                                </div>
                                            </td>

                                            <!-- Jumlah Soal -->
                                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black">
                                                    {{ $exam->questions_count }} Soal
                                                </span>
                                            </td>

                                            <!-- Status Akses -->
                                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                                @if($exam->status === 'published')
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700 shadow-2xs">
                                                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                        Draft
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-5 py-4">
                                                <div class="inline-flex items-center gap-2">
                                                    <span class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 font-mono text-sm font-bold tracking-widest text-slate-800">
                                                        {{ $exam->token }}
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Aksi Guru -->
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="inline-flex items-center gap-2">
                                                    <!-- Toggle Status Button -->
                                                    <form action="{{ route('guru.ujian.toggle_status', $exam->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @if($exam->status === 'published')
                                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition" onclick="return confirm('Tutup ujian ini? Siswa tidak akan dapat mengaksesnya.')">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                Tutup
                                                            </button>
                                                        @else
                                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition" onclick="return confirm('Mulai ujian ini sekarang agar siswa dapat mengerjakan?')">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                Mulai
                                                            </button>
                                                        @endif
                                                    </form>

                                                    <!-- Kelola Soal -->
                                                    <a href="{{ route('guru.ujian.show', $exam->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1.5 text-xs font-bold transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        Soal
                                                    </a>

                                                    <!-- Rekap Nilai -->
                                                    <a href="{{ route('guru.ujian.scores', $exam->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1.5 text-xs font-bold transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                                        Nilai
                                                    </a>

                                                    <!-- Hapus -->
                                                    <form action="{{ route('guru.ujian.destroy', $exam->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-xl text-rose-500 hover:bg-rose-50 hover:text-rose-700 transition" title="Hapus Ujian" onclick="return confirm('Hapus ujian ini beserta seluruh soalnya?')">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- ==================== MOBILE CARD VIEW (< md) ==================== -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @foreach($exams as $exam)
                                <div class="p-4 space-y-3 hover:bg-slate-50/60 transition">
                                    <!-- Card Header: Title & Status -->
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900 leading-snug">
                                                {{ $exam->title ?? 'Ujian ' . ($exam->subject->name ?? '-') }}
                                            </h4>
                                            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[11px] font-semibold">
                                                    {{ $exam->subject->name ?? '-' }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[11px] font-bold">
                                                    🏫 {{ $exam->classroom->name ?? '-' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            @if($exam->status === 'published')
                                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-extrabold text-emerald-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-[10px] font-semibold text-slate-600">
                                                    Draft
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Card Meta Details -->
                                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-500 pt-1">
                                        <div class="flex items-center gap-1.5 bg-slate-50 p-2 rounded-xl border border-slate-100">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span><strong>{{ $exam->duration }}</strong> Menit</span>
                                        </div>

                                        <div class="flex items-center gap-1.5 bg-slate-50 p-2 rounded-xl border border-slate-100">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span><strong>{{ $exam->questions_count }}</strong> Butir Soal</span>
                                        </div>
                                    </div>

                                    <!-- Card Action Buttons -->
                                    <div class="pt-2 flex flex-wrap items-center gap-2">
                                        <!-- Toggle Status -->
                                        <form action="{{ route('guru.ujian.toggle_status', $exam->id) }}" method="POST" class="flex-1 min-w-[120px]">
                                            @csrf
                                            @if($exam->status === 'published')
                                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 px-3 py-2 text-xs font-bold text-white shadow-2xs transition" onclick="return confirm('Tutup ujian ini? Siswa tidak akan dapat mengaksesnya.')">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Tutup Akses
                                                </button>
                                            @else
                                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3 py-2 text-xs font-bold text-white shadow-2xs transition" onclick="return confirm('Mulai ujian ini sekarang agar siswa dapat mengerjakan?')">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Mulai Ujian
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Kelola Soal -->
                                        <a href="{{ route('guru.ujian.show', $exam->id) }}" class="flex-1 min-w-[90px] flex items-center justify-center gap-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-2 text-xs font-bold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Soal ({{ $exam->questions_count }})
                                        </a>

                                        <!-- Rekap Nilai -->
                                        <a href="{{ route('guru.ujian.scores', $exam->id) }}" class="flex-1 min-w-[90px] flex items-center justify-center gap-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-2 text-xs font-bold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            Nilai ({{ $exam->sessions_count }})
                                        </a>

                                        <!-- Hapus Button -->
                                        <form action="{{ route('guru.ujian.destroy', $exam->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus Ujian" onclick="return confirm('Hapus ujian ini beserta seluruh soalnya?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination Links -->
                        <div class="px-6 py-4 border-t border-slate-100 bg-white">
                            {{ $exams->links() }}
                        </div>
                    @endif
                </div>

                <!-- Footer Note -->
                <footer class="pt-4 pb-8 text-center text-xs text-slate-400">
                    <p>&copy; {{ date('Y') }} CBT MTsN 12 Jakarta | Support by M1FDev</p>
                </footer>
            </main>
        </div>
    </div>
</x-app-layout>