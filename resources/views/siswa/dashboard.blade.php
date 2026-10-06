<x-app-layout :hide-nav="true">
    @php
        $completedSessions = $mySessions->filter(fn($s) => $s->isCompleted());
        $avgScore = $completedSessions->count() > 0 ? $completedSessions->avg('score') : 0;
        $ongoingSession = $mySessions->first(fn($s) => $s->status === 'ongoing');
    @endphp

    <div x-data="{ 
            activeTab: 'home', 
            mobileMenuOpen: false, 
            tokenModalOpen: false,
            profileModalOpen: false,
            inputTokenExamId: '{{ $activeExams->first()?->id ?? '' }}',
            inputTokenCode: ''
         }" 
         class="min-h-screen bg-slate-100/70 font-sans text-slate-800 antialiased pb-24 md:pb-12">

        <!-- ==================== TOP BLUE APP HEADER (Sesuai Foto Simaster) ==================== -->
        <header class="bg-[#2B77DE] bg-gradient-to-b from-[#2B77DE] to-[#1F67CB] text-white pt-4 pb-14 px-4 sm:px-6 lg:px-8 rounded-b-[2.5rem] shadow-md relative overflow-hidden">
            <!-- Background Decorative Circles -->
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute top-1/2 -left-10 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>

            <div class="max-w-4xl mx-auto relative z-10">
                <!-- Profile Section inside Header (Sesuai Foto) -->
                <div class="flex items-center gap-4 pt-1 pb-2">
                    <!-- Circular Avatar Silhouette -->
                    <div class="relative shrink-0">
                        <div class="w-16 h-16 rounded-full bg-white/20 border-2 border-white/60 flex items-center justify-center text-white overflow-hidden shadow-inner">
                            <svg class="w-10 h-10 text-white/90 translate-y-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- User Name, NISN, and Class Info -->
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base sm:text-lg font-bold text-white truncate leading-tight">
                            {{ Auth::user()->name }}
                        </h2>
                        <p class="text-xs font-mono text-white/80 mt-0.5 truncate tracking-wide">
                            {{ Auth::user()->nisn ?? '17/310790/SV/456738' }}
                        </p>
                        <p class="text-[11px] font-medium text-white/75 truncate mt-0.5">
                            {{ Auth::user()->classroom->name ?? 'Kelas X-A' }} &bull; MTsN 12 Jakarta
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- ==================== FLOATING STAT CARD (Sesuai Foto 3 Kolom) ==================== -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-9 relative z-20">
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/60 border border-slate-100 p-4 sm:p-5 flex items-center justify-around text-center divide-x divide-slate-100">
                <!-- Col 1: Exams / Ujian -->
                <div class="flex-1 px-2">
                    <span class="block text-xl sm:text-2xl font-black text-slate-800 font-mono">
                        {{ $activeExams->count() }}
                    </span>
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-0.5 block">
                        Ujian CBT
                    </span>
                </div>

                <!-- Col 2: Assignment / Selesai -->
                <div class="flex-1 px-2">
                    <span class="block text-xl sm:text-2xl font-black text-slate-800 font-mono">
                        {{ $completedSessions->count() }}
                    </span>
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-0.5 block">
                        Diselesaikan
                    </span>
                </div>

                <!-- Col 3: Score / Nilai Rata-rata -->
                <div class="flex-1 px-2">
                    <span class="block text-xl sm:text-2xl font-black text-slate-800 font-mono">
                        {{ number_format($avgScore, 0) }}
                    </span>
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-0.5 block">
                        Rata-rata Nilai
                    </span>
                </div>
            </div>
        </div>

        <!-- ==================== MAIN CONTENT BODY ==================== -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-7 space-y-7">

            <!-- Alerts Notifikasi -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-xs flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-xs flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Banner Sesi Berjalan (Jika Ada Ujian yang Sedang Dikerjakan) -->
            @if($ongoingSession)
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-4 text-white shadow-md flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                            <span class="w-3 h-3 bg-white rounded-full animate-ping"></span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-black tracking-widest text-white/90">Ujian Sedang Berlangsung</span>
                            <h4 class="text-sm font-bold">{{ $ongoingSession->exam->title ?? $ongoingSession->exam->subject->name }}</h4>
                        </div>
                    </div>
                    <a href="{{ route('siswa.ujian.show', $ongoingSession->exam_id) }}" 
                       class="px-4 py-2 bg-white text-orange-600 rounded-xl text-xs font-bold shadow-xs hover:bg-orange-50 transition shrink-0">
                        Lanjutkan
                    </a>
                </div>
            @endif

            <!-- ==================== SECTION 1: DAFTAR UJIAN CBT ==================== -->
            <div id="section-exams" class="pt-2 space-y-3.5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Ujian CBT Tersedia</h3>
                        <p class="text-[11px] text-slate-400">Pilih ujian untuk memasukkan token dan memulai pengerjaan.</p>
                    </div>
                </div>

                <!-- Filter Hari & Pencarian untuk Siswa -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
                    <!-- Form Filter Hari -->
                    <form method="GET" action="{{ route('siswa.dashboard') }}#section-exams" class="space-y-2.5">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-600">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Filter Jadwal Berdasarkan Hari:
                            </span>
                            @if(request()->hasAny(['day', 'search']))
                                <a href="{{ route('siswa.dashboard') }}" class="text-[11px] font-bold text-blue-600 hover:underline">
                                    Reset Semua
                                </a>
                            @endif
                        </div>

                        <!-- Horizontal Scrollable Day Chips -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                            <a href="{{ route('siswa.dashboard') }}#section-exams"
                               class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ !request('day') ? 'bg-[#2B77DE] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Semua Hari
                            </a>
                            @foreach($daysList as $d)
                                <a href="{{ route('siswa.dashboard', ['day' => $d, 'search' => request('search')]) }}#section-exams"
                                   class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ request('day') == $d ? 'bg-[#2B77DE] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    {{ $d }}
                                </a>
                            @endforeach
                        </div>

                        <!-- Search Bar -->
                        <div class="relative pt-1">
                            <span class="absolute inset-y-0 left-0 pt-1 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mata pelajaran / ujian..."
                                   class="w-full pl-8 pr-16 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-slate-50/50">
                            @if(request('day'))
                                <input type="hidden" name="day" value="{{ request('day') }}">
                            @endif
                            <button type="submit" class="absolute right-1 top-2 bottom-1 px-3 bg-[#2B77DE] text-white text-[11px] font-bold rounded-lg hover:bg-blue-700 transition">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>

                @if($activeExams->isEmpty())
                    <div class="bg-white p-8 rounded-2xl border border-slate-200/80 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                            <x-heroicon-o-folder class="w-6 h-6 text-blue-500" />
                        </div>
                        <h4 class="font-bold text-sm text-slate-800">Belum Ada Ujian untuk Kelas {{ Auth::user()->classroom->name ?? 'Anda' }}</h4>
                        <p class="text-xs text-slate-400 mt-1">Hanya ujian yang sesuai dengan kelas Anda ({{ Auth::user()->classroom->name ?? 'Belum Ditentukan' }}) yang akan ditampilkan di sini.</p>
                    </div>
                @else
                    <!-- ==================== RESPONSIVE CAROUSEL CARDS ==================== -->
                    <div x-data="{
                        scrollLeft: 0,
                        maxScroll: 0,
                        init() {
                            this.$nextTick(() => { this.updateScrollInfo(); });
                        },
                        updateScrollInfo() {
                            const el = this.$refs.carousel;
                            if (!el) return;
                            this.scrollLeft = el.scrollLeft;
                            this.maxScroll = el.scrollWidth - el.clientWidth;
                        },
                        scroll(direction) {
                            const el = this.$refs.carousel;
                            if (!el) return;
                            const cardWidth = el.querySelector('div[data-carousel-card]')?.offsetWidth || 300;
                            const scrollAmount = cardWidth + 16;
                            el.scrollBy({
                                left: direction === 'next' ? scrollAmount : -scrollAmount,
                                behavior: 'smooth'
                            });
                        }
                    }" class="relative group">

                        <!-- Left Nav Button (Desktop) -->
                        <button type="button" 
                                @click="scroll('prev')" 
                                x-show="scrollLeft > 10" 
                                x-transition
                                class="hidden md:flex absolute -left-3.5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/95 text-slate-700 shadow-md border border-slate-200/80 items-center justify-center hover:bg-slate-50 hover:scale-105 active:scale-95 transition"
                                aria-label="Sebelumnya">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <!-- Right Nav Button (Desktop) -->
                        <button type="button" 
                                @click="scroll('next')" 
                                x-show="maxScroll > 10 && scrollLeft < (maxScroll - 10)" 
                                x-transition
                                class="hidden md:flex absolute -right-3.5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/95 text-slate-700 shadow-md border border-slate-200/80 items-center justify-center hover:bg-slate-50 hover:scale-105 active:scale-95 transition"
                                aria-label="Selanjutnya">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        <!-- Carousel Track Container -->
                        <div x-ref="carousel"
                             @scroll.passive="updateScrollInfo()"
                             class="flex gap-4 overflow-x-auto pb-4 pt-1 px-1 snap-x snap-mandatory scroll-smooth scrollbar-none"
                             style="-webkit-overflow-scrolling: touch;">
                            @foreach($activeExams as $exam)
                                @php
                                    $sess = $mySessions->get($exam->id);
                                @endphp
                                <div data-carousel-card 
                                     class="snap-start shrink-0 w-[84%] sm:w-[50%] md:w-[46%] lg:w-[40%] bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md transition p-4 sm:p-5 flex flex-col justify-between space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700">
                                                {{ $exam->subject->name ?? 'Mapel' }}
                                            </span>
                                            <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                                @if($exam->hasNotStartedYet())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        ⏳ Belum Dimulai
                                                    </span>
                                                @endif
                                                @if($exam->day_of_week)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                                        <x-heroicon-o-calendar /> {{ $exam->day_of_week }}
                                                    </span>
                                                @endif
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700" title="Kelas Sasaran Ujian: {{ $exam->all_classroom_names }}">
                                                    {{ $exam->all_classroom_names }}
                                                </span>
                                            </div>
                                        </div>

                                        <h4 class="font-bold text-sm sm:text-base text-slate-900 leading-snug line-clamp-2">
                                            {{ $exam->title ?? 'Ujian ' . $exam->subject->name }}
                                        </h4>

                                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-500 pt-2 border-t border-slate-100">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="truncate">Jam: <strong class="text-slate-800">{{ $exam->formatted_time_range }}</strong></span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span><strong>{{ $exam->questions_count }}</strong> Soal</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-2">
                                        @if(!$sess)
                                            @if($exam->hasNotStartedYet())
                                                <a href="{{ route('siswa.ujian.token', $exam->id) }}" 
                                                   class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold uppercase tracking-wider shadow-xs transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Belum Dimulai ({{ substr($exam->start_time, 0, 5) }} WIB)</span>
                                                </a>
                                            @elseif($exam->hasEnded())
                                                <div class="w-full py-2.5 px-4 rounded-xl bg-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider text-center border border-slate-200">
                                                    Waktu Ujian Berakhir
                                                </div>
                                            @else
                                                <a href="{{ route('siswa.ujian.token', $exam->id) }}" 
                                                   class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-[#2B77DE] hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider shadow-xs transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                                    <span>Mulai Ujian (Token)</span>
                                                </a>
                                            @endif
                                        @elseif($sess->isCompleted())
                                            <div class="flex items-center justify-between gap-2 bg-emerald-50 p-2.5 rounded-xl border border-emerald-100">
                                                <span class="text-xs font-bold text-emerald-800 flex items-center gap-1">
                                                    Selesai ({{ number_format($sess->score, 1) }})
                                                </span>
                                                <a href="{{ route('siswa.ujian.hasil', $exam->id) }}" 
                                                   class="text-xs font-black text-emerald-700 hover:underline">
                                                    Lihat Nilai
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ route('siswa.ujian.show', $exam->id) }}" 
                                               class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold uppercase tracking-wider shadow-xs transition animate-pulse">
                                                <span>Lanjutkan Pengerjaan</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </main>

        <!-- ==================== MODAL QUICK INPUT TOKEN ==================== -->
        <div x-show="tokenModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click="tokenModalOpen = false">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative" @click.stop>
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h3 class="font-black text-lg text-slate-900">Masukkan Token Ujian</h3>
                    <p class="text-xs text-slate-500">Minta token ujian kepada guru pengawas sebelum memulai.</p>
                </div>

                @if($activeExams->isNotEmpty())
                    <form method="POST" :action="'/siswa/ujian/' + inputTokenExamId + '/verify-token'" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Ujian</label>
                            <select x-model="inputTokenExamId" class="w-full text-xs font-medium rounded-xl border-slate-300 py-2.5">
                                @foreach($activeExams as $ex)
                                    <option value="{{ $ex->id }}">{{ $ex->title ?? $ex->subject->name }} ({{ $ex->all_classroom_names }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Token</label>
                            <input type="text" name="token" x-model="inputTokenCode" required maxlength="15"
                                   placeholder="Contoh: MTK24"
                                   @input="inputTokenCode = inputTokenCode.toUpperCase()"
                                   class="w-full text-center font-mono font-black text-lg tracking-widest uppercase rounded-xl border-slate-300 py-2.5 text-indigo-700 focus:border-indigo-500">
                        </div>

                        <div class="pt-2 flex items-center gap-2">
                            <button type="button" @click="tokenModalOpen = false" class="w-1/2 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">
                                Batal
                            </button>
                            <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-[#2B77DE] hover:bg-blue-700 text-white text-xs font-bold shadow-sm">
                                Masuk Ujian
                            </button>
                        </div>
                    </form>
                @else
                    <div class="mt-4 text-center text-xs text-slate-500">
                        Belum ada paket ujian yang aktif saat ini.
                    </div>
                @endif
            </div>
        </div>

        <!-- ==================== MODAL PROFIL SISWA ==================== -->
        <div x-show="profileModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click="profileModalOpen = false">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative text-center space-y-4" @click.stop>
                <div class="w-16 h-16 rounded-full bg-[#2B77DE] text-white flex items-center justify-center mx-auto text-xl font-black shadow-md">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>

                <div>
                    <h3 class="font-extrabold text-base text-slate-900">{{ Auth::user()->name }}</h3>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ Auth::user()->email }}</p>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 text-left text-xs space-y-2 border border-slate-100">
                    <div class="flex justify-between">
                        <span class="text-slate-400">NISN / No. Induk:</span>
                        <strong class="text-slate-700 font-mono">{{ Auth::user()->nisn ?? '-' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kelas:</span>
                        <strong class="text-blue-700">{{ Auth::user()->classroom->name ?? 'Belum Ditentukan' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Sekolah:</span>
                        <strong class="text-slate-700">MTsN 12 Jakarta</strong>
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <form method="POST" action="{{ route('logout') }}" class="w-full inline">
                        @csrf
                        <button type="submit" class="w-full py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ==================== FLOATING BOTTOM APP NAVIGATION DOCK (Persis seperti Foto) ==================== -->
        <nav class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-2 shadow-2xl">
            <div class="max-w-md mx-auto flex items-center justify-around relative">
                <!-- 1. Home Icon -->
                <button @click="activeTab = 'home'; window.scrollTo({ top: 0, behavior: 'smooth' })" 
                        class="flex flex-col items-center justify-center p-2 text-slate-500 hover:text-[#2B77DE] transition"
                        :class="activeTab === 'home' ? 'text-[#2B77DE]' : ''">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </button>

                <!-- 5. User Profile Icon -->
                <button @click="profileModalOpen = true" 
                        class="flex flex-col items-center justify-center p-2 text-slate-500 hover:text-[#2B77DE] transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </button>
            </div>
        </nav>

    </div>
</x-app-layout>
