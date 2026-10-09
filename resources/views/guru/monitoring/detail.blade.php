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
                    <span class="font-extrabold text-slate-800 text-sm">Analisis Jawaban Siswa</span>
                </div>
                <a href="{{ route('guru.monitoring.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Kembali</span>
                </a>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <a href="{{ route('guru.monitoring.index') }}" class="text-xs font-bold text-slate-400 hover:text-indigo-600 transition uppercase tracking-wider">Monitoring</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-indigo-600">Detail Jawaban {{ $session->user->name ?? 'Siswa' }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">

                <!-- Header Breadcrumbs & Back Navigation -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>Hasil & Analisis Jawaban Siswa</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Rincian nomor soal yang dijawab salah atau benar serta komparasi kunci jawaban.
                        </p>
                    </div>

                    <div>
                        <a href="{{ route('guru.monitoring.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold shadow-2xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            <span>Kembali ke Monitoring</span>
                        </a>
                    </div>
                </div>

                <!-- Student & Score Summary Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs p-6 sm:p-7">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        
                        <!-- Profil Siswa & Info Ujian -->
                        <div class="flex items-start gap-4">
                            @php
                                $userName = $session->user->name ?? 'Siswa';
                                $parts = preg_split('/\s+/', trim($userName));
                                $initials = count($parts) >= 2 
                                    ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1))
                                    : strtoupper(mb_substr($parts[0], 0, 2));
                            @endphp
                            <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white font-black text-xl flex items-center justify-center shrink-0 shadow-md">
                                {{ $initials }}
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ $userName }}</h2>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $session->user->classroom->name ?? '-' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 font-mono">NISN: {{ $session->user->nisn ?? '-' }} &bull; {{ $session->user->email }}</p>
                                <p class="text-xs font-semibold text-slate-600 pt-1">
                                    Ujian: <span class="text-slate-900 font-bold">{{ $exam->title }}</span> ({{ $exam->subject->name ?? '-' }})
                                </p>
                            </div>
                        </div>

                        <!-- Skor & Status Badge -->
                        <div class="flex flex-wrap items-center gap-4 lg:border-l lg:border-slate-100 lg:pl-8">
                            <div class="text-center sm:text-left">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Nilai Akhir</span>
                                <div class="flex items-baseline gap-2 mt-0.5">
                                    @if($session->isCompleted())
                                        <span class="text-3xl sm:text-4xl font-black text-slate-900">
                                            {{ number_format($session->score, 1) }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-400">/ 100</span>
                                    @else
                                        <span class="text-sm font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-xl border border-amber-200">
                                            Sedang Mengerjakan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2.5 text-center">
                                <div class="px-3.5 py-2 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                                    <span class="text-[10px] font-bold uppercase tracking-wider block text-emerald-600">Benar</span>
                                    <span class="text-lg font-black">{{ $correctCount }}</span>
                                </div>
                                <div class="px-3.5 py-2 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800">
                                    <span class="text-[10px] font-bold uppercase tracking-wider block text-rose-600">Salah</span>
                                    <span class="text-lg font-black">{{ $wrongCount }}</span>
                                </div>
                                <div class="px-3.5 py-2 rounded-2xl bg-slate-100 border border-slate-200 text-slate-700">
                                    <span class="text-[10px] font-bold uppercase tracking-wider block text-slate-500">Kosong</span>
                                    <span class="text-lg font-black">{{ $unansweredCount }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Anti-Cheat Integrity Status Banner -->
                @if($session->violation_count >= 4 || $session->is_cheating_detected)
                    <div class="p-6 bg-rose-50/90 border-2 border-rose-300 rounded-3xl shadow-xs">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm animate-pulse">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="flex-1 space-y-1.5">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <h3 class="text-base sm:text-lg font-black text-rose-900 tracking-tight">
                                        TERDETEKSI KECURANGAN: SISWA {{ $session->violation_count }}x KELUAR DARI UJIAN
                                    </h3>
                                    <span class="px-3 py-0.5 rounded-full text-xs font-black bg-rose-600 text-white">
                                        STATUS: CURANG
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm text-rose-800 leading-relaxed">
                                    Siswa ini telah terdeteksi meninggalkan layar ujian, beralih ke tab browser lain, atau membuka aplikasi pihak ketiga sebanyak <strong>{{ $session->violation_count }} kali</strong> (telah mencapai/melebihi batas toleransi 4 kali).
                                    @if($session->last_violation_at)
                                        <span class="block text-xs text-rose-700/80 mt-1 font-mono">Pelanggaran terakhir tercatat pada: {{ $session->last_violation_at->format('d/m/Y H:i:s') }} WIB</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($session->violation_count > 0)
                    <div class="p-5 bg-amber-50/90 border border-amber-300 rounded-3xl shadow-xs">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm sm:text-base font-extrabold text-amber-900">
                                        Peringatan Integritas: {{ $session->violation_count }}x Keluar Layar Ujian
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-200 text-amber-900">
                                        Toleransi Tersisa: {{ max(0, 4 - $session->violation_count) }}x
                                    </span>
                                </div>
                                <p class="text-xs text-amber-800">
                                    Siswa terdeteksi keluar dari layar ujian sebanyak {{ $session->violation_count }} kali. Sistem akan otomatis menandai <strong>"Terdeteksi Curang"</strong> jika mencapai 4 kali keluar.
                                    @if($session->last_violation_at)
                                        <span class="block text-xs text-amber-700/80 mt-0.5 font-mono">Pelanggaran terakhir: {{ $session->last_violation_at->format('d/m/Y H:i:s') }} WIB</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-emerald-50/80 border border-emerald-200/90 rounded-2xl flex items-center gap-3 text-xs text-emerald-800">
                        <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <span class="font-bold text-emerald-900">Integritas Ujian Tertib:</span> Siswa mengerjakan secara jujur tanpa meninggalkan layar ujian (0x pelanggaran).
                        </div>
                    </div>
                @endif

                <!-- ==================== TABEL KOTAK NOMOR SOAL (MATRIX GRID) ==================== -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs p-6 sm:p-7 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>Matriks Kotak Hasil Jawaban Soal</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">Klik nomor kotak untuk melompat langsung ke tinjauan butir soal di bawah.</p>
                        </div>

                        <!-- Legend Keterangan Warna Kotak -->
                        <div class="flex items-center gap-3 text-xs font-bold">
                            <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                <span class="w-3.5 h-3.5 rounded-md bg-emerald-500 text-white flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                Benar ({{ $correctCount }})
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                                <span class="w-3.5 h-3.5 rounded-md bg-rose-500 text-white flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                                Salah ({{ $wrongCount }})
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                <span class="w-3 h-3 rounded-md bg-slate-300 text-slate-600 text-[9px] flex items-center justify-center font-bold">-</span>
                                Kosong ({{ $unansweredCount }})
                            </span>
                        </div>
                    </div>

                    <!-- Kotak-kotak Matrix Soal -->
                    <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 gap-2.5 pt-2">
                        @foreach($analysis as $item)
                            @php
                                if ($item['status'] === 'correct') {
                                    $boxClass = 'bg-emerald-500 hover:bg-emerald-600 text-white border-emerald-600 shadow-emerald-500/20';
                                    $symbol = '✓';
                                } elseif ($item['status'] === 'wrong') {
                                    $boxClass = 'bg-rose-500 hover:bg-rose-600 text-white border-rose-600 shadow-rose-500/20';
                                    $symbol = '✕';
                                } else {
                                    $boxClass = 'bg-slate-100 hover:bg-slate-200 text-slate-600 border-slate-300';
                                    $symbol = '-';
                                }
                            @endphp
                            <a href="#soal-{{ $item['number'] }}" 
                               class="group flex flex-col items-center justify-center p-2 rounded-2xl border-2 shadow-2xs transition transform hover:-translate-y-0.5 {{ $boxClass }}"
                               title="Soal Nomor {{ $item['number'] }} ({{ ucfirst($item['status']) }})">
                                <span class="text-xs font-black">{{ $item['number'] }}</span>
                                <span class="text-[10px] font-bold opacity-90">{{ $symbol }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- ==================== TABEL DETAIL SOAL & JAWABAN SISWA ==================== -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="font-extrabold text-base text-slate-900">
                            Tabel Detail Komparasi Soal, Pilihan Siswa, & Kunci Jawaban
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar lengkap butir soal beserta analisis jawaban yang dipilih siswa.</p>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($analysis as $item)
                            @php
                                $q = $item['question'];
                                $isCorrect = ($item['status'] === 'correct');
                                $isWrong = ($item['status'] === 'wrong');
                                $isUnanswered = ($item['status'] === 'unanswered');
                            @endphp
                            <div id="soal-{{ $item['number'] }}" class="p-6 sm:p-7 hover:bg-slate-50/50 transition">
                                <div class="flex items-start gap-4">
                                    
                                    <!-- Nomor & Kotak Status Badge -->
                                    <div class="flex flex-col items-center gap-1.5 shrink-0">
                                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black text-white shadow-xs
                                            {{ $isCorrect ? 'bg-emerald-500' : ($isWrong ? 'bg-rose-500' : 'bg-slate-400') }}">
                                            {{ $item['number'] }}
                                        </div>
                                        @if($isCorrect)
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                Benar ✓
                                            </span>
                                        @elseif($isWrong)
                                            <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                                                Salah ✕
                                            </span>
                                        @else
                                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                                                Kosong
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Konten Soal & Opsi -->
                                    <div class="space-y-4 flex-1 min-w-0">
                                        <!-- Teks Soal -->
                                        <div>
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pertanyaan Soal:</span>
                                            <p class="text-slate-900 font-semibold text-sm sm:text-base leading-relaxed whitespace-pre-line mt-1">
                                                {{ $q->question_text }}
                                            </p>
                                        </div>

                                        <!-- Gambar Soal Jika Ada -->
                                        @if($q->image)
                                            <div>
                                                <a href="{{ asset('storage/' . $q->image) }}" target="_blank" class="inline-block group relative">
                                                    <img src="{{ asset('storage/' . $q->image) }}" alt="Gambar Soal" class="max-h-52 rounded-2xl border border-slate-200 object-contain bg-white shadow-2xs group-hover:opacity-95 transition">
                                                    <span class="absolute bottom-2 right-2 bg-black/70 text-white text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                        Perbesar
                                                    </span>
                                                </a>
                                            </div>
                                        @endif

                                        <!-- Grid Pilihan Jawaban A, B, C, D -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                            @foreach(['A', 'B', 'C', 'D'] as $opt)
                                                @php
                                                    $field = 'option_' . strtolower($opt);
                                                    $isThisKey = ($q->correct_answer === $opt);
                                                    $isThisSelected = ($item['selected'] === $opt);
                                                    
                                                    // Styling kartu opsi
                                                    if ($isThisSelected && $isThisKey) {
                                                        // Siswa memilih opsi ini DAN opsi ini benar
                                                        $cardStyle = 'border-emerald-500 bg-emerald-50/80 text-emerald-950 font-bold ring-2 ring-emerald-500/20';
                                                        $badgeStyle = 'bg-emerald-600 text-white';
                                                    } elseif ($isThisSelected && !$isThisKey) {
                                                        // Siswa memilih opsi ini TETAPI salah
                                                        $cardStyle = 'border-rose-400 bg-rose-50 text-rose-950 font-semibold ring-2 ring-rose-400/20';
                                                        $badgeStyle = 'bg-rose-600 text-white';
                                                    } elseif (!$isThisSelected && $isThisKey) {
                                                        // Kunci jawaban asli yang tidak dipilih siswa
                                                        $cardStyle = 'border-emerald-400/80 bg-emerald-50/40 text-emerald-900 font-semibold dashed border-2';
                                                        $badgeStyle = 'bg-emerald-100 text-emerald-800';
                                                    } else {
                                                        $cardStyle = 'border-slate-200 bg-white text-slate-700';
                                                        $badgeStyle = 'bg-slate-100 text-slate-600';
                                                    }
                                                @endphp
                                                <div class="p-3 rounded-2xl border text-xs sm:text-sm flex items-start gap-2.5 transition {{ $cardStyle }}">
                                                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $badgeStyle }}">
                                                        {{ $opt }}
                                                    </span>
                                                    <div class="flex-1 min-w-0">
                                                        <p>{{ $q->$field }}</p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            @if($isThisSelected)
                                                                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider {{ $isThisKey ? 'text-emerald-700' : 'text-rose-600' }}">
                                                                    ● Dipilih Siswa
                                                                </span>
                                                            @endif
                                                            @if($isThisKey)
                                                                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-emerald-700">
                                                                    ✓ Kunci Jawaban
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Rangkuman Hasil Komparasi Singkat -->
                                        <div class="p-3.5 rounded-2xl text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2 border
                                            {{ $isCorrect ? 'bg-emerald-50/60 border-emerald-200 text-emerald-900' : ($isWrong ? 'bg-rose-50/60 border-rose-200 text-rose-900' : 'bg-slate-50 border-slate-200 text-slate-700') }}">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold">Jawaban Siswa:</span>
                                                @if($item['selected'])
                                                    <span class="px-2.5 py-0.5 rounded-lg font-black text-xs {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                                                        Opsi {{ $item['selected'] }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic">Tidak Dijawab (Kosong)</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="font-bold">Kunci Benar:</span>
                                                <span class="px-2.5 py-0.5 rounded-lg font-black text-xs bg-emerald-600 text-white">
                                                    Opsi {{ $q->correct_answer }}
                                                </span>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </main>
        </div>
    </div>
</x-app-layout>
