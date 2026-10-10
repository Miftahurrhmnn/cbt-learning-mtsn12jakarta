<x-app-layout :hide-nav="true">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

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
                    <span class="font-extrabold text-slate-800 text-sm">
                        {{ $selectedExam ? $selectedExam->title : ($selectedSubject ? 'Bank Soal: ' . $selectedSubject->name : 'Bank Soal Guru') }}
                    </span>
                </div>
                @if($selectedExam || $selectedSubject)
                    <a href="{{ route('guru.bank-soal.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
                        &larr; Ganti Ujian
                    </a>
                @else
                    <a href="{{ route('guru.ujian.create') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition">
                        <span>Buat Ujian</span>
                    </a>
                @endif
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Portal Guru</span>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('guru.bank-soal.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition">Bank Soal</a>
                    @if($selectedSubject)
                        <span class="text-slate-300">/</span>
                        <span class="text-xs font-bold text-slate-600">{{ $selectedSubject->name }}</span>
                    @endif
                    @if($selectedExam)
                        <span class="text-slate-300">/</span>
                        <span class="text-xs font-bold text-indigo-600">{{ $selectedExam->title }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">



                @if(!$selectedExam && !$selectedSubject)
                    <!-- ==================== TAMPILAN 1: GRID KARTU PER UJIAN (DEFAULT) ==================== -->
                    <!-- Sesuai permintaan: Card diganti menjadi nama mata pelajaran & ujiannya, detail jam & tanggal, kelas berapa, dan dibedakan tiap ujian cardnya -->
                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                                <span>Bank Soal Guru</span>
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                                Pilih salah satu paket ujian di bawah untuk melihat butir soal serta kunci jawabannya.
                            </p>
                        </div>
                    </div>

                    <!-- Stats Overview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-5 bg-purple-700 rounded-3xl border border-slate-200/80 shadow-2xs flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-white">Ujian Terdaftar</span>
                                <p class="text-2xl font-black text-white mt-0.5">{{ $exams->count() }}</p>
                            </div>
                        </div>

                        <div class="p-5 bg-emerald-700 rounded-3xl border border-slate-200/80 shadow-2xs flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-white">Mata Pelajaran Anda</span>
                                <p class="text-2xl font-black text-white mt-0.5">{{ $subjects->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Toolbar Filter & Pencarian Ujian -->
                    <div class="p-4 sm:p-5 bg-white rounded-3xl border border-slate-200/80 shadow-2xs space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h2 class="text-base font-extrabold text-slate-800">Daftar Paket Ujian & Bank Soal</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Tiap kartu ujian menampilkan mata pelajaran, judul ujian, jadwal jam, tanggal, serta kelas sasaran.</p>
                            </div>
                        </div>

                        <!-- Filter Bar -->
                        <form action="{{ route('guru.bank-soal.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1 items-center">
                            <div class="sm:col-span-8 relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" name="search_exam" value="{{ request('search_exam') }}" 
                                    placeholder="Cari nama ujian atau mata pelajaran..." 
                                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            </div>

                            <div class="sm:col-span-3">
                                <select name="filter_subject_id" onchange="this.form.submit()" 
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition bg-white">
                                    <option value="">Semua Ujian</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}" {{ request('filter_subject_id') == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->name }} ({{ $sub->exams_count }} Ujian)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1 flex items-center gap-1.5">
                                <button type="submit" class="w-full p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition flex items-center justify-center shadow-xs" title="Cari">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </button>
                                @if(request()->filled('search_exam') || request()->filled('filter_subject_id'))
                                    <a href="{{ route('guru.bank-soal.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Grid Kartu Ujian (Dibedakan Tiap Ujian, Lengkap Mapel, Ujian, Jam & Tanggal, Kelas) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @forelse($exams as $exam)
                            <div class="group relative bg-white rounded-3xl border border-slate-200/90 hover:border-slate-500 p-6 shadow-2xs hover:shadow-xl hover:shadow-slate-500/10 transition-all duration-200 flex flex-col justify-between transform hover:-translate-y-1">
                                <div>
                                    <!-- Top Row: Badge Mapel & Status/Soal -->
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-black bg-slate-50 text-slate-700 border border-slate-100">
                                            {{ $exam->subject->name ?? 'Mata Pelajaran' }}
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $exam->questions_count }} Soal
                                            </span>
                                            @if($exam->status === 'published')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                    Draft
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Nama Ujian (Title) -->
                                    <h3 class="text-base sm:text-lg font-black text-slate-900 group-hover:text-slate-600 transition leading-snug mb-3">
                                        {{ $exam->title }}
                                    </h3>

                                    <!-- Detail List: Jam, Tanggal & Kelas Sasaran -->
                                    <div class="space-y-2 bg-slate-50/70 p-3.5 rounded-2xl border border-slate-200/70 text-xs">
                                        <!-- Tanggal & Hari Pelaksanaan -->
                                        <div class="flex items-center gap-2 text-slate-700">
                                            <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="font-medium text-slate-600">
                                                <strong class="text-slate-800">{{ $exam->day_of_week ? $exam->day_of_week . ', ' : '' }}{{ $exam->exam_date ? $exam->exam_date->format('d M Y') : 'Jadwal Fleksibel' }}</strong>
                                            </span>
                                        </div>

                                        <!-- Jam Pelaksanaan -->
                                        <div class="flex items-center gap-2 text-slate-800 font-bold">
                                            <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Jam: {{ $exam->formatted_time_range }}</span>
                                        </div>

                                        <!-- Detail Kelas Sasaran -->
                                        <div class="flex items-center gap-2 text-slate-700 pt-1 border-t border-slate-200/60">
                                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span class="truncate">Akses Kelas: <strong class="text-indigo-900 font-extrabold">{{ $exam->all_classroom_names }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Bottom Link -->
                                <a href="{{ route('guru.bank-soal.index', ['exam_id' => $exam->id]) }}" 
                                   class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-500 group-hover:text-blue-600">
                                    <span>Buka Soal & Jawaban</span>
                                    <span class="text-base group-hover:translate-x-1.5 transition-transform duration-200">&rarr;</span>
                                </a>
                            </div>
                        @empty
                            <div class="col-span-full p-12 text-center bg-white rounded-3xl border border-slate-200">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-800">Belum Ada Paket Ujian Terdaftar</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Silakan buat paket ujian baru terlebih dahulu untuk mulai memasukkan butir soal dan mengelola bank soal.
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('guru.ujian.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-xs">
                                        <span>+ Buat Ujian Baru</span>
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                @else
                    <!-- ==================== TAMPILAN 2: DAFTAR SOAL & KUNCI JAWABAN UJIAN/MAPEL TERPILIH ==================== -->
                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                                <span>Bank Soal: {{ $selectedSubject ? $selectedSubject->name : '' }}{{ $selectedExam ? ' - ' . $selectedExam->title : '' }}</span>
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                                Menampilkan {{ $totalQuestions }} butir soal serta kunci jawaban yang telah dibuat.
                            </p>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0">
                            <a href="{{ route('guru.bank-soal.index') }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition active:scale-[0.98]">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Kembali ke Daftar Kartu Ujian</span>
                            </a>
                            @if($selectedExam)
                                <a href="{{ route('guru.ujian.soal.create', $selectedExam->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-xs">
                                    <span>Tambah Soal Manual</span>
                                </a>
                            @else
                                <a href="{{ route('guru.ujian.create') }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-xs">
                                    <span>Buat Ujian</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Summary Info Banner Jika Ujian Terpilih -->
                    @if($selectedExam)
                        <div class="bg-white p-5 rounded-3xl shadow-2xs border border-slate-200/80 grid grid-cols-2 md:grid-cols-4 items-center justify-center gap-4">
                            <div>
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Mata Pelajaran</span>
                                <p class="text-sm font-extrabold text-slate-900 mt-1">{{ $selectedExam->subject->name ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Nama Ujian</span>
                                <p class="text-sm font-extrabold text-indigo-700 mt-1">{{ $selectedExam->title }}</p>
                            </div>
                            <div>
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Target Kelas</span>
                                <p class="text-sm font-extrabold text-slate-900 mt-1">{{ $selectedExam->all_classroom_names }}</p>
                            </div>
                            <div>
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Jadwal & Jam</span>
                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $selectedExam->day_of_week ? $selectedExam->day_of_week . ', ' : '' }}{{ $selectedExam->exam_date ? $selectedExam->exam_date->format('d M Y') : '-' }}
                                    <span class="block text-indigo-600 font-mono mt-0.5">{{ $selectedExam->formatted_time_range }}</span>
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Toolbar Pencarian Butir Soal -->
                    <div class="p-4 sm:p-5 bg-white rounded-3xl border border-slate-200/80 shadow-2xs">
                        <form action="{{ route('guru.bank-soal.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-center">
                            @if($selectedExam)
                                <input type="hidden" name="exam_id" value="{{ $selectedExam->id }}">
                            @endif
                            @if($selectedSubject)
                                <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                            @endif

                            <!-- Search Box -->
                            <div class="sm:col-span-11 relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Cari teks pertanyaan butir soal atau opsi jawaban..." 
                                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            </div>

                            <!-- Tombol Cari & Reset -->
                            <div class="sm:col-span-1 flex items-center gap-1.5">
                                <button type="submit" class="w-full p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition flex items-center justify-center shadow-xs" title="Cari">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </button>
                                @if(request()->filled('search'))
                                    <a href="{{ route('guru.bank-soal.index', array_filter(['exam_id' => $selectedExam?->id, 'subject_id' => $selectedSubject?->id])) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Questions List Container with Questions, Options, & Answer Key -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                        @if($questions->isEmpty())
                            <div class="p-12 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900">Belum Ada Butir Soal Terdaftar</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    @if($selectedExam)
                                        Ujian "{{ $selectedExam->title }}" belum memiliki butir soal. Silakan tambah soal manual atau impor dari file Word .docx.
                                    @else
                                        Belum ada butir soal untuk mata pelajaran ini.
                                    @endif
                                </p>
                                <div class="mt-4 flex items-center justify-center gap-3">
                                    @if($selectedExam)
                                        <a href="{{ route('guru.ujian.soal.create', $selectedExam->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold">
                                            <span>Tambah Soal Manual</span>
                                        </a>
                                        <a href="{{ route('guru.ujian.show', $selectedExam->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 rounded-xl text-xs font-bold border border-blue-200">
                                            <span>Impor File DOCX</span>
                                        </a>
                                    @else
                                        <a href="{{ route('guru.ujian.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">
                                            <span>Buat Ujian Baru</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="divide-y divide-slate-100">
                                @foreach($questions as $index => $q)
                                    <div class="p-5 sm:p-6 hover:bg-slate-50/50 transition">
                                        <div class="flex items-start justify-between gap-4">
                                            
                                            <div class="flex items-start gap-3.5 flex-1 min-w-0">
                                                <!-- Nomor Badge Bulat Khas CBT -->
                                                <div class="w-8 h-8 rounded-full bg-[#2B77DE] text-white font-extrabold flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                                    {{ $questions->firstItem() + $index }}
                                                </div>

                                                <div class="space-y-3 flex-1 min-w-0">
                                                    <!-- Meta tags: Mapel & Ujian -->
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        @if($q->subject)
                                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                                {{ $q->subject->name }}
                                                            </span>
                                                        @elseif($selectedSubject)
                                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                                {{ $selectedSubject->name }}
                                                            </span>
                                                        @endif
                                                        @if($q->exam)
                                                            <span class="text-slate-300">&bull;</span>
                                                            <a href="{{ route('guru.ujian.show', $q->exam->id) }}" class="text-[11px] font-semibold text-slate-600 hover:text-indigo-600 transition truncate max-w-xs">
                                                                {{ $q->exam->title }}
                                                            </a>
                                                            @if($q->exam->classroom)
                                                                <span class="text-slate-300">&bull;</span>
                                                                <span class="text-[11px] text-slate-400 font-medium">
                                                                    {{ $q->exam->all_classroom_names }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </div>

                                                    <!-- Teks Soal -->
                                                    <p class="text-slate-900 font-medium text-sm sm:text-base leading-relaxed whitespace-pre-line">
                                                        {{ $q->question_text }}
                                                    </p>

                                                    <!-- Gambar Soal Jika Ada -->
                                                    @if($q->image)
                                                        <div class="my-2">
                                                            <a href="{{ asset('storage/' . $q->image) }}" target="_blank" class="inline-block group relative">
                                                                <img src="{{ asset('storage/' . $q->image) }}" alt="Gambar Soal" class="max-h-44 rounded-xl border border-slate-200 object-contain bg-white shadow-xs group-hover:opacity-95 transition">
                                                                <span class="absolute bottom-2 right-2 bg-black/70 text-white text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                                    Lihat
                                                                </span>
                                                            </a>
                                                        </div>
                                                    @endif

                                                    <!-- Pilihan Opsi A, B, C, D dengan Penanda Kunci Jawaban -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                                        @foreach(['A', 'B', 'C', 'D'] as $opt)
                                                            @php
                                                                $field = 'option_' . strtolower($opt);
                                                                $isKey = ($q->correct_answer === $opt);
                                                            @endphp
                                                            <div class="p-2.5 rounded-xl border text-xs flex items-start gap-2 transition {{ $isKey ? 'border-emerald-500 bg-emerald-50/70 text-emerald-950 font-semibold shadow-2xs' : 'border-slate-200 bg-white text-slate-700' }}">
                                                                <span class="w-5 h-5 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $isKey ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                                                                    {{ $opt }}
                                                                </span>
                                                                <span class="flex-1 min-w-0">{{ $q->$field }}</span>
                                                                @if($isKey)
                                                                    <span class="text-[11px] text-emerald-700 font-black ml-auto shrink-0 flex items-center gap-1">
                                                                        ✓ Kunci Jawaban
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Aksi: Edit & Hapus -->
                                            <div class="flex items-center gap-1 shrink-0">
                                                <a href="{{ route('guru.soal.edit', $q->id) }}" 
                                                   class="p-2 text-slate-400 hover:text-indigo-600 rounded-xl hover:bg-indigo-50 transition" 
                                                   title="Edit Soal">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form action="{{ route('guru.soal.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus butir soal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition" title="Hapus Soal">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination Footer -->
                            @if($questions->hasPages())
                                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                                    {{ $questions->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

            </main>
        </div>
    </div>
</x-app-layout>
