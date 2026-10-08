<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian: {{ $exam->title ?? $exam->subject->name }} - CBT Modern</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col antialiased no-select"
    x-data="cbtExam({
        examId: {{ $exam->id }},
        totalQuestions: {{ $questions->count() }},
        questions: {{ Js::from($questions) }},
        initialAnswers: {{ Js::from($userAnswers) }},
        initialRemainingSeconds: {{ $remainingSeconds }},
        initialViolationCount: {{ $session->violation_count ?? 0 }},
        initialIsCheating: {{ ($session->isCheating() || ($session->violation_count ?? 0) >= 4) ? 'true' : 'false' }},
        saveUrl: '{{ route('siswa.ujian.simpan_jawaban', $exam->id) }}',
        finishUrl: '{{ route('siswa.ujian.selesai', $exam->id) }}',
        violationUrl: '{{ route('siswa.ujian.log_violation', $exam->id) }}'
    })"
    x-init="initExam()">

    <!-- Modern Sticky Topbar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white font-black text-sm flex items-center justify-center shadow-sm">
                    CBT
                </div>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 line-clamp-1">
                        {{ $exam->title ?? 'Ujian ' . $exam->subject->name }}
                    </h1>
                </div>
            </div>

                <!-- Countdown Timer & Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Status Jam Ujian -->
                <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Jam: <strong class="text-slate-900">{{ $exam->formatted_time_range }}</strong></span>
                </div>

                <!-- Tombol Layar Penuh (Fullscreen) -->
                <button type="button" @click="enterFullscreen()" 
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer active:scale-95"
                    :class="isFullscreen ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-rose-50 text-rose-700 border border-rose-200 animate-pulse'"
                    :title="isFullscreen ? 'Mode Layar Penuh Aktif' : 'Aktifkan Mode Layar Penuh'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5m-6 11l-5 5m0 0h4m-4 0v-4m16 4v-4m0 4h-4m0 0l-5-5"/>
                    </svg>
                    <span class="hidden md:inline" x-text="isFullscreen ? 'Layar Penuh' : 'Aktifkan Fullscreen'"></span>
                </button>

                <!-- Tombol Refresh Ujian -->
                <button type="button" @click="refreshPage()" 
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 border border-slate-200 text-xs font-bold transition shadow-2xs cursor-pointer active:scale-95" 
                    title="Muat Ulang Halaman Ujian (Refresh)">
                    <svg class="w-3.5 h-3.5 text-slate-600 transition" :class="isRefreshing ? 'animate-spin text-indigo-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="hidden sm:inline">Refresh</span>
                </button>

                <!-- Timer Display -->
                <div class="flex items-center space-x-2 px-3 sm:px-4 py-2 rounded-xl border font-mono font-bold text-sm tracking-wider shadow-xs transition"
                    :class="remainingSeconds < 300 ? 'bg-rose-50 text-rose-600 border-rose-300 animate-pulse' : 'bg-slate-50 text-slate-800 border-slate-200'">
                    <svg class="w-4 h-4" :class="remainingSeconds < 300 ? 'text-rose-600' : 'text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="formattedTime"></span>
                </div>

                <!-- Tombol Mobile Toggle Drawer Nomor Soal -->
                <button type="button" @click="mobileDrawer = true"
                    class="lg:hidden inline-flex items-center px-3 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-bold border border-indigo-200 shadow-xs">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span x-text="answeredCount + '/' + totalQuestions"></span>
                </button>

                <!-- Tombol Selesai Ujian Desktop -->
                <button type="button" @click="showFinishModal = true"
                    class="hidden sm:inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xs transition hover:shadow-md">
                    Selesai Ujian
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Lembar Soal (Kiri) -->
            <div class="lg:col-span-8 flex flex-col justify-between space-y-6">
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">

                    <!-- Bar Nomor & Status Simpan -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg bg-indigo-600 text-white font-black text-sm shadow-xs">
                                Soal No. <span x-text="currentIndex + 1" class="ml-1"></span>
                            </span>
                            <span class="text-xs text-slate-400 font-medium">
                                dari <strong x-text="totalQuestions"></strong> Soal
                            </span>
                        </div>

                        <!-- Indikator Simpan Realtime -->
                        <div class="flex items-center text-xs font-semibold">
                            <template x-if="isSaving">
                                <span class="text-amber-600 flex items-center bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                                    <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-amber-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Menyimpan Jawaban...
                                </span>
                            </template>
                            <template x-if="!isSaving && saveStatus">
                                <span class="text-emerald-700 flex items-center bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 animate-fade">
                                    <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Tersimpan
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Teks Soal -->
                    <div class="space-y-5">
                        <div class="text-slate-900 font-medium text-base sm:text-lg leading-relaxed whitespace-pre-line select-text"
                            x-text="currentQuestion.question_text">
                        </div>

                        <!-- Gambar Soal (Jika Ada) -->
                        <template x-if="currentQuestion.image">
                            <div class="my-4 p-3 bg-slate-50 rounded-xl border border-slate-200 inline-block max-w-full">
                                <img :src="'/storage/' + currentQuestion.image"
                                    alt="Gambar Soal"
                                    class="max-h-80 w-auto rounded-lg object-contain cursor-zoom-in hover:opacity-95 transition shadow-xs"
                                    @click="openImageModal('/storage/' + currentQuestion.image)">
                                <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 mt-1.5 italic font-medium">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    Klik gambar untuk melihat ukuran penuh
                                </span>
                            </div>
                        </template>

                        <!-- Pilihan Opsi A, B, C, D -->
                        <div class="space-y-3 pt-4">
                            <template x-for="option in ['A', 'B', 'C', 'D']" :key="option">
                                <label class="group flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all duration-150 relative"
                                    :class="answers[currentQuestion.id] === option ? 'border-indigo-600 bg-indigo-50/50 shadow-sm ring-2 ring-indigo-500/20' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/50 bg-white'"
                                    @click="selectAnswer(currentQuestion.id, option)">
                                    
                                    <div class="w-8 h-8 rounded-lg font-bold text-xs flex items-center justify-center flex-shrink-0 mr-3.5 transition"
                                        :class="answers[currentQuestion.id] === option ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200 border border-slate-200'"
                                        x-text="option">
                                    </div>

                                    <div class="flex-1 text-sm sm:text-base text-slate-800 font-medium pt-1 select-text"
                                        x-text="currentQuestion['option_' + option.toLowerCase()]">
                                    </div>

                                    <template x-if="answers[currentQuestion.id] === option">
                                        <div class="text-indigo-600 ml-2 mt-1">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        </div>
                                    </template>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Tombol Navigasi Bawah -->
                <div class="flex items-center justify-between bg-white p-4 rounded-2xl shadow-xs border border-slate-200/80 gap-4">
                    <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0"
                        class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl disabled:opacity-40 disabled:cursor-not-allowed transition">
                        Soal Sebelumnya
                    </button>

                    <button type="button" @click="showFinishModal = true"
                        class="sm:hidden inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Selesai Ujian
                    </button>

                    <button type="button" @click="nextQuestion()" :disabled="currentIndex === totalQuestions - 1"
                        class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl disabled:opacity-40 disabled:cursor-not-allowed transition shadow-xs">
                        Soal Selanjutnya
                    </button>
                </div>
            </div>

            <!-- Panel Kotak Nomor Soal Desktop (Kanan) -->
            <div class="hidden lg:block lg:col-span-4 space-y-6">
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sticky top-24">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-900">Navigasi Nomor Soal</h3>
                        <span class="text-xs text-slate-500 font-medium">
                            <span class="font-extrabold text-emerald-600 text-sm" x-text="answeredCount">0</span> / <span x-text="totalQuestions"></span> Terjawab
                        </span>
                    </div>

                    <!-- Keterangan Warna Sesuai Permintaan -->
                    <!-- Awalnya MERAH, menjadi HIJAU jika sudah dijawab -->
                    <div class="grid grid-cols-2 gap-2 text-xs mb-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-3.5 h-3.5 rounded bg-rose-500 flex-shrink-0"></div>
                            <span class="text-slate-600 font-medium text-[11px]">Belum Dijawab (Merah)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3.5 h-3.5 rounded bg-emerald-600 flex-shrink-0"></div>
                            <span class="text-slate-600 font-medium text-[11px]">Sudah Dijawab (Hijau)</span>
                        </div>
                    </div>

                    <!-- Grid Kotak-Kotak 1, 2, 3, 4, ... -->
                    <div class="grid grid-cols-5 gap-2.5 max-h-[380px] overflow-y-auto p-1">
                        <template x-for="(q, idx) in questions" :key="q.id">
                            <button type="button"
                                @click="goToQuestion(idx)"
                                class="h-11 rounded-xl font-bold text-sm flex items-center justify-center transition-all duration-150 relative shadow-xs"
                                :class="[
                                    // Warna: Hijau jika ada jawaban, Merah jika belum ada jawaban
                                    answers[q.id] ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-rose-500 hover:bg-rose-600 text-white',
                                    // Highlight fokus pada nomor yang sedang aktif
                                    currentIndex === idx ? 'ring-4 ring-indigo-400 ring-offset-2 scale-105 z-10' : ''
                                ]">
                                <span x-text="idx + 1"></span>
                                <!-- Badge opsi terpilih -->
                                <template x-if="answers[q.id]">
                                    <span class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-white text-emerald-700 text-[10px] font-black flex items-center justify-center shadow-xs border border-emerald-100"
                                        x-text="answers[q.id]">
                                    </span>
                                </template>
                            </button>
                        </template>
                    </div>

                    <!-- Tombol Selesai Ujian di Sidebar -->
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <button type="button" @click="showFinishModal = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm rounded-xl shadow-xs transition hover:shadow-md active:scale-[0.99]">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Selesaikan Ujian
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Mobile Drawer untuk Kotak Nomor Soal (Slide-over) -->
    <div x-show="mobileDrawer" x-cloak class="fixed inset-0 z-50 lg:hidden overflow-hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="mobileDrawer = false"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-xs bg-white shadow-2xl p-6 flex flex-col justify-between" @click.stop>
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Daftar Nomor Soal</h3>
                            <p class="text-xs text-slate-500"><span class="text-emerald-600 font-bold" x-text="answeredCount"></span> dari <span x-text="totalQuestions"></span> terjawab</p>
                        </div>
                        <button type="button" @click="mobileDrawer = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[11px] mb-4 p-2 bg-slate-50 rounded-lg">
                        <div class="flex items-center space-x-1.5">
                            <div class="w-3 h-3 rounded bg-rose-500"></div>
                            <span>Belum (Merah)</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-3 h-3 rounded bg-emerald-600"></div>
                            <span>Dijawab (Hijau)</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-2 max-h-[60vh] overflow-y-auto p-1">
                        <template x-for="(q, idx) in questions" :key="q.id">
                            <button type="button"
                                @click="goToQuestion(idx); mobileDrawer = false"
                                class="h-10 rounded-lg font-bold text-xs flex items-center justify-center relative shadow-xs"
                                :class="[
                                    answers[q.id] ? 'bg-emerald-600 text-white' : 'bg-rose-500 text-white',
                                    currentIndex === idx ? 'ring-2 ring-indigo-400 ring-offset-1' : ''
                                ]">
                                <span x-text="idx + 1"></span>
                                <template x-if="answers[q.id]">
                                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-white text-emerald-700 text-[9px] font-black flex items-center justify-center"
                                        x-text="answers[q.id]">
                                    </span>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="button" @click="mobileDrawer = false; showFinishModal = true"
                        class="w-full py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider">
                        Selesaikan Ujian
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Selesai Ujian -->
    <div x-show="showFinishModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showFinishModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="showFinishModal = false">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showFinishModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6 sm:p-8">
                
                <div class="sm:flex sm:items-start">
                    <template x-if="unansweredCount === 0">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 sm:mx-0 sm:h-12 sm:w-12 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </template>
                    <template x-if="unansweredCount > 0">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-100 sm:mx-0 sm:h-12 sm:w-12 text-rose-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </template>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                            <span x-show="unansweredCount === 0">Konfirmasi Selesai Ujian</span>
                            <span x-show="unansweredCount > 0" class="text-rose-600">Ujian Belum Dapat Diselesaikan!</span>
                        </h3>
                        <div class="mt-2 text-sm text-slate-600 space-y-2.5">
                            <template x-if="unansweredCount === 0">
                                <p>
                                    Luar biasa! Seluruh <strong class="text-emerald-600" x-text="totalQuestions"></strong> butir soal telah berhasil Anda jawab. Apakah Anda yakin ingin menyelesaikan ujian sekarang?
                                </p>
                            </template>

                            <template x-if="unansweredCount > 0">
                                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs leading-relaxed space-y-1">
                                    <p class="font-bold flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Masih ada soal yang belum dikerjakan (kotak merah)!
                                    </p>
                                    <p>Sistem mewajibkan seluruh soal dijawab terlebih dahulu sebelum Anda diizinkan untuk menyelesaikan ujian.</p>
                                </div>
                            </template>

                            <p class="text-xs text-slate-400">
                                Setelah dikirim, sesi pengerjaan akan ditutup dan nilai akhir akan otomatis dikalkulasi.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 sm:flex sm:flex-row-reverse gap-3">
                    <template x-if="unansweredCount === 0">
                        <form :action="finishUrl" method="POST" @submit="isSubmitting = true; stopAlarmSound()" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-emerald-600 text-sm font-bold text-white hover:bg-emerald-700 transition">
                                Ya, Kirim & Selesaikan
                            </button>
                        </form>
                    </template>

                    <template x-if="unansweredCount > 0">
                        <button type="button" @click="goToFirstUnanswered()"
                            class="w-full inline-flex justify-center items-center gap-1.5 rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 transition">
                            Cari Soal Yang Belum
                        </button>
                    </template>

                    <button type="button" @click="showFinishModal = false"
                        class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 shadow-xs px-5 py-2.5 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Gambar Zoom -->
    <div x-show="modalImage" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-xs p-4" @click="modalImage = null">
        <div class="relative max-w-4xl max-h-screen p-2" @click.stop>
            <button type="button" @click="modalImage = null" class="absolute -top-3 -right-3 bg-white text-slate-900 rounded-full w-8 h-8 flex items-center justify-center font-bold shadow-lg hover:bg-slate-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <img :src="modalImage" class="max-h-[85vh] max-w-full rounded-xl object-contain bg-white shadow-2xl">
        </div>
    </div>

    <!-- ==================== MODAL WAJIB LAYAR PENUH (FULLSCREEN PROMPT) ==================== -->
    <div x-show="showFullscreenPromptModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"></div>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 text-center shadow-2xl border border-slate-200 space-y-5">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto shadow-inner border border-indigo-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5m-6 11l-5 5m0 0h4m-4 0v-4m16 4v-4m0 4h-4m0 0l-5-5"/></svg>
                </div>
                
                <div class="space-y-2">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Integritas & Anti-Kecurangan
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Wajib Mode Layar Penuh
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                        Ujian ini diawasi dengan sistem deteksi kecurangan otomatis. Selama ujian berlangsung, layar akan dikunci dalam <strong>Mode Layar Penuh (Fullscreen) tanpa menu navigasi</strong>.
                    </p>
                </div>

                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-left text-xs text-amber-900 space-y-2">
                    <div class="font-extrabold flex items-center gap-1.5 text-amber-950">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Peraturan Ketat Ujian CBT:</span>
                    </div>
                    <ul class="space-y-1 list-disc list-inside text-amber-800">
                        <li>Dilarang keluar dari layar ujian, membuka tab lain, atau aplikasi pihak ketiga.</li>
                        <li>Sirene alarm peringatan akan berbunyi kencang jika Anda meninggalkan layar ujian.</li>
                        <li>Jika terdeteksi keluar hingga <strong>4 kali</strong>, sistem otomatis menandai Anda <strong>TERDETEKSI CURANG</strong> pada monitor Guru.</li>
                    </ul>
                </div>

                <button type="button" @click="startFullscreenExam()"
                    class="w-full py-4 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-indigo-600/25 transition active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5m-6 11l-5 5m0 0h4m-4 0v-4m16 4v-4m0 4h-4m0 0l-5-5"/></svg>
                    <span>Masuk Layar Penuh & Mulai Ujian</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL ALARM & PERINGATAN KECURANGAN ==================== -->
    <div x-show="showViolationModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-rose-950/90 backdrop-blur-md transition-opacity"></div>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 text-center shadow-2xl border-4 border-rose-500 space-y-5 animate-pulse-short">
                
                <!-- Siren Alert Icon -->
                <div class="relative mx-auto w-20 h-20 flex items-center justify-center">
                    <span class="absolute inset-0 rounded-full bg-rose-500/20 animate-ping"></span>
                    <div class="w-16 h-16 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-lg shadow-rose-600/40">
                        <svg class="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                </div>

                <!-- Judul & Keterangan -->
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-300 inline-block animate-pulse">
                        PERINGATAN INTEGRITAS CBT
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-rose-600 tracking-tight">
                        PELANGGARAN TERDETEKSI!
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                        Anda terdeteksi meninggalkan layar ujian, beralih ke tab browser lain, atau membuka aplikasi pihak ketiga.
                    </p>
                </div>

                <!-- Indikator Pelanggaran & Status Curang (>= 4x) -->
                <template x-if="violationCount >= 4 || isCheating">
                    <div class="p-4 bg-rose-100 border-2 border-rose-500 rounded-2xl text-left space-y-2">
                        <div class="flex items-center gap-2 text-rose-900 font-black text-sm">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>STATUS: TERDETEKSI CURANG (<span x-text="violationCount"></span>x KELUAR)</span>
                        </div>
                        <p class="text-xs text-rose-800 leading-relaxed">
                            Perhatian Serius! Anda telah keluar dari layar ujian sebanyak <strong x-text="violationCount"></strong> kali (mencapai batas toleransi 4 kali). 
                            <strong>Sistem telah menandai dan melaporkan Anda sebagai TERDETEKSI CURANG pada monitor Guru!</strong>
                        </p>
                    </div>
                </template>

                <template x-if="violationCount > 0 && violationCount < 4 && !isCheating">
                    <div class="p-4 bg-amber-50 border border-amber-300 rounded-2xl text-left space-y-1.5 text-xs text-amber-900">
                        <div class="flex items-center justify-between font-bold">
                            <span>Pelanggaran Ke: <strong class="text-rose-600 text-sm" x-text="violationCount"></strong> / 3 Toleransi</span>
                            <span class="bg-amber-200 text-amber-900 px-2 py-0.5 rounded-md font-black text-[10px]" x-text="'Sisa: ' + (4 - violationCount) + 'x'"></span>
                        </div>
                        <p class="text-slate-600">
                            Peringatan! Jika Anda keluar hingga <strong>4 kali</strong>, sistem akan otomatis mengunci status <strong>"TERDETEKSI CURANG"</strong> pada monitoring Guru.
                        </p>
                    </div>
                </template>

                <!-- Tombol Kembali ke Ujian -->
                <button type="button" @click="dismissViolationModal()"
                    class="w-full py-4 px-6 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-rose-600/30 transition active:scale-[0.98] cursor-pointer">
                    Saya Mengerti, Kembali ke Ujian (Layar Penuh)
                </button>
            </div>
        </div>
    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function cbtExam(config) {
            return {
                examId: config.examId,
                totalQuestions: config.totalQuestions,
                questions: config.questions,
                answers: config.initialAnswers || {},
                currentIndex: 0,
                remainingSeconds: config.initialRemainingSeconds,
                saveUrl: config.saveUrl,
                finishUrl: config.finishUrl,
                violationUrl: config.violationUrl,
                
                // State Integritas & Anti-Cheat
                violationCount: config.initialViolationCount || 0,
                isCheating: config.initialIsCheating || false,
                isFullscreen: false,
                showFullscreenPromptModal: false,
                showViolationModal: false,
                audioCtx: null,
                alarmInterval: null,
                lastViolationTimestamp: 0,
                isSubmitting: false,

                isSaving: false,
                isRefreshing: false,
                saveStatus: false,
                showFinishModal: false,
                mobileDrawer: false,
                modalImage: null,
                timerInterval: null,

                refreshPage() {
                    this.isRefreshing = true;
                    window.location.reload();
                },

                get currentQuestion() {
                    return this.questions[this.currentIndex] || {};
                },

                get answeredCount() {
                    return Object.keys(this.answers).filter(k => this.answers[k] !== null && this.answers[k] !== '').length;
                },

                get unansweredCount() {
                    return this.totalQuestions - this.answeredCount;
                },

                get formattedTime() {
                    const hours = Math.floor(this.remainingSeconds / 3600);
                    const minutes = Math.floor((this.remainingSeconds % 3600) / 60);
                    const seconds = this.remainingSeconds % 60;

                    const pad = (num) => String(num).padStart(2, '0');
                    if (hours > 0) {
                        return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                    }
                    return `${pad(minutes)}:${pad(seconds)}`;
                },

                initTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.remainingSeconds > 0) {
                            this.remainingSeconds--;
                        } else {
                            clearInterval(this.timerInterval);
                            this.autoSubmit();
                        }
                    }, 1000);
                },

                // Inisialisasi Ujian & Event Listener Keamanan Anti-Cheat
                initExam() {
                    this.initTimer();

                    // Cek status Fullscreen awal
                    this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
                    if (!this.isFullscreen) {
                        this.showFullscreenPromptModal = true;
                    }

                    // 1. Deteksi Fullscreen Change (Tekan Esc / F11 untuk keluar layar penuh)
                    const onFullscreenChange = () => {
                        this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
                        if (!this.isFullscreen && !this.showFinishModal && !this.showFullscreenPromptModal && !this.isSubmitting) {
                            this.handleViolation('Keluar dari mode layar penuh (fullscreen)');
                        }
                    };
                    document.addEventListener('fullscreenchange', onFullscreenChange);
                    document.addEventListener('webkitfullscreenchange', onFullscreenChange);
                    document.addEventListener('msfullscreenchange', onFullscreenChange);

                    // 2. Deteksi Beralih Tab / Minimalkan Browser (Visibility Change)
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden && !this.showFinishModal && !this.isSubmitting) {
                            this.handleViolation('Beralih tab browser atau meminimalkan jendela');
                        }
                    });

                    // 3. Deteksi Membuka Aplikasi Lain / Klik di Luar Browser (Window Blur)
                    window.addEventListener('blur', () => {
                        if (!this.showFinishModal && !this.showFullscreenPromptModal && !this.isSubmitting) {
                            this.handleViolation('Membuka aplikasi pihak ketiga atau mengklik di luar jendela ujian');
                        }
                    });

                    // 4. Cegah Klik Kanan (Context Menu)
                    document.addEventListener('contextmenu', (e) => {
                        e.preventDefault();
                        return false;
                    });

                    // 5. Cegah Shortcut Inspeksi / Developer Tools (F12, Ctrl+Shift+I, Ctrl+U)
                    window.addEventListener('keydown', (e) => {
                        if (
                            e.key === 'F12' ||
                            (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'C' || e.key === 'c' || e.key === 'J' || e.key === 'j')) ||
                            (e.ctrlKey && (e.key === 'u' || e.key === 'U'))
                        ) {
                            e.preventDefault();
                            this.handleViolation('Mencoba membuka inspect element / developer tools');
                            return false;
                        }
                    });
                },

                // Mengaktifkan Fullscreen dari Dialog Awal
                startFullscreenExam() {
                    this.showFullscreenPromptModal = false;
                    this.enterFullscreen();
                    this.initAudioContext();
                },

                // Meminta Browser Masuk Fullscreen
                enterFullscreen() {
                    const docEl = document.documentElement;
                    const requestMethod = docEl.requestFullscreen || docEl.webkitRequestFullscreen || docEl.msRequestFullscreen;
                    if (requestMethod) {
                        requestMethod.call(docEl).then(() => {
                            this.isFullscreen = true;
                        }).catch(() => {
                            this.isFullscreen = false;
                        });
                    }
                },

                // Menyiapkan Web Audio Context untuk Alarm
                initAudioContext() {
                    try {
                        const AudioCtxClass = window.AudioContext || window.webkitAudioContext;
                        if (!this.audioCtx) {
                            this.audioCtx = new AudioCtxClass();
                        }
                        if (this.audioCtx.state === 'suspended') {
                            this.audioCtx.resume();
                        }
                    } catch (e) {
                        console.warn('Web Audio API not supported:', e);
                    }
                },

                // Memainkan Suara Alarm Sirene Polisi/Darurat Berkala
                playAlarmSound() {
                    if (this.alarmInterval) return;
                    this.initAudioContext();

                    const triggerSiren = () => {
                        if (!this.showViolationModal || !this.audioCtx) return;
                        try {
                            const now = this.audioCtx.currentTime;
                            const osc = this.audioCtx.createOscillator();
                            const gain = this.audioCtx.createGain();

                            osc.type = 'sawtooth';
                            // Modulasi frekuensi 750Hz -> 1150Hz -> 750Hz (Sirene Darurat)
                            osc.frequency.setValueAtTime(750, now);
                            osc.frequency.linearRampToValueAtTime(1150, now + 0.25);
                            osc.frequency.linearRampToValueAtTime(750, now + 0.5);

                            gain.gain.setValueAtTime(0.28, now);
                            gain.gain.linearRampToValueAtTime(0.01, now + 0.5);

                            osc.connect(gain);
                            gain.connect(this.audioCtx.destination);

                            osc.start(now);
                            osc.stop(now + 0.52);
                        } catch (err) {
                            console.error('Error playing alarm:', err);
                        }
                    };

                    triggerSiren();
                    this.alarmInterval = setInterval(triggerSiren, 550);
                },

                // Mematikan Suara Alarm
                stopAlarmSound() {
                    if (this.alarmInterval) {
                        clearInterval(this.alarmInterval);
                        this.alarmInterval = null;
                    }
                },

                // Menutup Modal Peringatan & Kembali Masuk Fullscreen
                dismissViolationModal() {
                    this.stopAlarmSound();
                    this.showViolationModal = false;
                    this.enterFullscreen();
                },

                // Handler Pencatatan Pelanggaran
                async handleViolation(reason) {
                    const now = Date.now();
                    // Debounce cooldown 2.5 detik agar satu aksi (alt-tab) tidak mencatat ganda dari blur & visibilitychange
                    if (now - this.lastViolationTimestamp < 2500) {
                        return;
                    }
                    this.lastViolationTimestamp = now;

                    // Tampilkan modal dan mainkan sirene alarm
                    this.showViolationModal = true;
                    this.playAlarmSound();

                    // Kirim log pelanggaran ke backend server
                    try {
                        const response = await fetch(this.violationUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ reason: reason })
                        });

                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.violationCount = data.violation_count;
                            if (data.is_cheating_detected || this.violationCount >= 4) {
                                this.isCheating = true;
                            }
                        }
                    } catch (error) {
                        console.error('Gagal mengirim log pelanggaran:', error);
                        this.violationCount++;
                        if (this.violationCount >= 4) {
                            this.isCheating = true;
                        }
                    }
                },

                goToQuestion(index) {
                    if (index >= 0 && index < this.totalQuestions) {
                        this.currentIndex = index;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                goToFirstUnanswered() {
                    for (let i = 0; i < this.totalQuestions; i++) {
                        const q = this.questions[i];
                        if (q && (!this.answers[q.id] || this.answers[q.id] === '')) {
                            this.goToQuestion(i);
                            this.showFinishModal = false;
                            this.mobileDrawer = false;
                            return;
                        }
                    }
                    this.showFinishModal = false;
                },

                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.goToQuestion(this.currentIndex - 1);
                    }
                },

                nextQuestion() {
                    if (this.currentIndex < this.totalQuestions - 1) {
                        this.goToQuestion(this.currentIndex + 1);
                    }
                },

                async selectAnswer(questionId, selectedOption) {
                    // Update state lokal seketika: kotak seketika berubah warna dari merah jadi hijau!
                    this.answers[questionId] = selectedOption;
                    this.isSaving = true;
                    this.saveStatus = false;

                    try {
                        const response = await fetch(this.saveUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                question_id: questionId,
                                selected_answer: selectedOption
                            })
                        });

                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.saveStatus = true;
                            setTimeout(() => { this.saveStatus = false; }, 2000);
                        } else if (data.timeout) {
                            alert('Waktu pengerjaan ujian telah berakhir.');
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Gagal menyimpan jawaban:', error);
                    } finally {
                        this.isSaving = false;
                    }
                },

                autoSubmit() {
                    this.isSubmitting = true;
                    this.stopAlarmSound();
                    alert('Waktu ujian telah berakhir! Ujian Anda akan diselesaikan otomatis.');
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.finishUrl;
                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(token);
                    document.body.appendChild(form);
                    form.submit();
                },

                openImageModal(src) {
                    this.modalImage = src;
                }
            };
        }
    </script>
</body>
</html>
