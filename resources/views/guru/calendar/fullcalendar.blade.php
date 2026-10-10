<x-app-layout :hide-nav="true">
    {{-- CSS FullCalendar --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css" media="print" />

    {{-- Kustomisasi Tema Kalender agar cocok dengan Tailwind (Modern/Flat Design) --}}
    <style>
        #calendar { font-family: inherit; }
        .fc-unthemed th, .fc-unthemed td, .fc-unthemed thead, .fc-unthemed tbody, .fc-unthemed .fc-divider, .fc-unthemed .fc-row, .fc-unthemed .fc-content, .fc-unthemed .fc-popover, .fc-unthemed .fc-list-view, .fc-unthemed .fc-list-heading td {
            border-color: #f1f5f9; /* slate-100 */
        }
        .fc-toolbar {
            margin-bottom: 1.5rem !important;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .fc-toolbar h2 {
            font-size: 1.25rem; font-weight: 800; color: #0f172a; /* slate-900 */ text-transform: capitalize;
            letter-spacing: -0.02em;
        }
        .fc-button {
            background: #ffffff; border: 1px solid #e2e8f0; color: #475569; text-shadow: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); height: 38px; border-radius: 0.75rem; padding: 0 16px; font-weight: 700; font-size: 0.75rem; text-transform: capitalize; transition: all 0.15s ease;
        }
        .fc-button:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
        .fc-state-active { background: #4f46e5 !important; color: #ffffff !important; border-color: #4f46e5 !important; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.25) !important; }
        .fc-state-disabled { opacity: 0.5; cursor: not-allowed; }
        .fc-event {
            border: none; border-radius: 8px; padding: 3px 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: transform 0.12s ease, box-shadow 0.12s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .fc-event:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.15);
        }
        .fc-day-header { padding: 12px 0 !important; background-color: #f8fafc; color: #64748b; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0 !important; }
        .fc-today { background-color: #f5f3ff !important; } /* Soft purple-50 for current day */
        .color-choice-btn.selected {
            ring-width: 3px;
            ring-offset-width: 2px;
            transform: scale(1.1);
        }
    </style>

    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
        <!-- Guru Sidebar Component -->
        <x-guru-sidebar />

        <div class="flex-1 min-w-0 flex flex-col min-h-screen">
            <!-- Mobile Sticky Top Header -->
            <header class="md:hidden sticky top-0 z-20 flex items-center justify-between px-4 py-3 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <button @click="sidebarOpen = true" class="p-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-slate-900 shadow-2xs transition" aria-label="Buka Menu Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span class="font-extrabold text-slate-800 text-sm">Kalender & Catatan Guru</span>
                </div>
                <button type="button" onclick="openCreateModal()" class="p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-2xs transition" title="Tambah Catatan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-4 ml-auto">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>
            
            {{-- Konten Utama --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
                <!-- Header Judul Halaman & Action Button -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                            <span>Kalender Kegiatan dan Catatan</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Kelola jadwal ujian, rapat, tenggat tugas, serta simpan catatan khusus dengan warna visual.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="openCreateModal()" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-500/20 hover:shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Catatan Baru</span>
                        </button>
                    </div>
                </div>

                <!-- Panduan Warna Catatan -->
                <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-2xs flex flex-wrap items-center gap-3 text-xs">
                    <span class="font-bold text-slate-700">Panduan Warna Catatan:</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> Umum / Kegiatan
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-semibold border border-emerald-100">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Ujian / Penilaian
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-700 font-semibold border border-amber-100">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Tenggat / Penting
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-rose-50 text-rose-700 font-semibold border border-rose-100">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span> Mendesak
                    </span>
                </div>

                <!-- Card Kalender Utama -->
                <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-6 lg:p-8">
                        <div id="calendar"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- ==================== MODAL TAMBAH & EDIT CATATAN ==================== -->
    <div id="eventModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop Blur -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" onclick="closeEventModal()"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 text-left shadow-2xl border border-slate-200 space-y-5 animate-scale-up" onclick="event.stopPropagation()">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div id="modalIconContainer" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 id="modalTitle" class="text-lg font-black text-slate-900 tracking-tight">Tambah Catatan Kegiatan</h3>
                            <p class="text-xs text-slate-400">Atur judul, catatan keterangan, dan warna label</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeEventModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form Fields -->
                <form id="eventForm" class="space-y-4">
                    <input type="hidden" id="event_id" value="">

                    <!-- Judul Kegiatan -->
                    <div>
                        <label for="event_title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Judul Kegiatan / Catatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="event_title" required placeholder="Contoh: Rapat Dewan Guru / PH Bab 2" 
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
                    </div>

                    <!-- Catatan / Deskripsi Tambahan -->
                    <div>
                        <label for="event_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Isi Catatan / Keterangan</span>
                            <span class="text-[11px] text-slate-400 font-normal">Opsional</span>
                        </label>
                        <textarea id="event_description" rows="3" placeholder="Tuliskan catatan detail agenda..."
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs resize-none"></textarea>
                    </div>

                    <!-- Pilihan Warna Catatan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Pilih Warna Catatan</span>
                            <span id="selectedColorLabel" class="text-xs font-bold text-indigo-600">Indigo (Default)</span>
                        </label>
                        <input type="hidden" id="event_color" value="#4f46e5">

                        <!-- Palette Bulatan Warna -->
                        <div class="flex items-center gap-2.5 flex-wrap p-2.5 bg-slate-50 rounded-2xl border border-slate-200">
                            <!-- Preset colors -->
                            <button type="button" onclick="selectColor('#4f46e5', 'Indigo (Default)')" 
                                class="color-btn w-8 h-8 rounded-full bg-[#4f46e5] flex items-center justify-center text-white transition hover:scale-110 shadow-xs ring-2 ring-indigo-500 ring-offset-2" data-color="#4f46e5">
                                <svg class="w-4 h-4 check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button type="button" onclick="selectColor('#2563eb', 'Biru (Ujian / Tes)')" 
                                class="color-btn w-8 h-8 rounded-full bg-[#2563eb] flex items-center justify-center text-white transition hover:scale-110 shadow-xs" data-color="#2563eb">
                                <svg class="w-4 h-4 check-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button type="button" onclick="selectColor('#059669', 'Hijau (Selesai / Santai)')" 
                                class="color-btn w-8 h-8 rounded-full bg-[#059669] flex items-center justify-center text-white transition hover:scale-110 shadow-xs" data-color="#059669">
                                <svg class="w-4 h-4 check-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button type="button" onclick="selectColor('#d97706', 'Amber (Deadline / Penting)')" 
                                class="color-btn w-8 h-8 rounded-full bg-[#d97706] flex items-center justify-center text-white transition hover:scale-110 shadow-xs" data-color="#d97706">
                                <svg class="w-4 h-4 check-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button type="button" onclick="selectColor('#dc2626', 'Merah (Mendesak / Libur)')" 
                                class="color-btn w-8 h-8 rounded-full bg-[#dc2626] flex items-center justify-center text-white transition hover:scale-110 shadow-xs" data-color="#dc2626">
                                <svg class="w-4 h-4 check-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button type="button" onclick="selectColor('#7c3aed', 'Ungu (Rapat / Agenda)')" 
                                class="color-btn w-8 h-8 rounded-full bg-[#7c3aed] flex items-center justify-center text-white transition hover:scale-110 shadow-xs" data-color="#7c3aed">
                                <svg class="w-4 h-4 check-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button type="button" onclick="selectColor('#0d9488', 'Teal (KBM / Tugas)')" 
                                class="color-btn w-8 h-8 rounded-full bg-[#0d9488] flex items-center justify-center text-white transition hover:scale-110 shadow-xs" data-color="#0d9488">
                                <svg class="w-4 h-4 check-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button type="button" onclick="selectColor('#475569', 'Slate (Catatan Pribadi)')" 
                                class="color-btn w-8 h-8 rounded-full bg-[#475569] flex items-center justify-center text-white transition hover:scale-110 shadow-xs" data-color="#475569">
                                <svg class="w-4 h-4 check-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>

                            <!-- Custom Color Picker -->
                            <div class="relative flex items-center ml-auto">
                                <label for="event_custom_color" class="cursor-pointer text-[11px] font-bold text-slate-500 hover:text-indigo-600 flex items-center gap-1.5 px-2 py-1 bg-white rounded-lg border border-slate-200">
                                    <input type="color" id="event_custom_color" value="#4f46e5" onchange="selectCustomColor(this.value)" class="w-5 h-5 rounded cursor-pointer border-0 p-0">
                                    <span>Kustom</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Pelaksanaan -->
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label for="event_start" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Mulai <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="event_start" required
                                class="block w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
                        </div>
                        <div>
                            <label for="event_end" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Selesai <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="event_end" required
                                class="block w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Tombol Aksi Modal -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeEventModal()" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-bold text-xs transition">
                            Batal
                        </button>
                        <button type="button" id="saveEventBtn" onclick="saveEvent()" 
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition active:scale-95 flex items-center gap-2 cursor-pointer">
                            <span id="saveBtnText">Simpan Catatan</span>
                            <svg id="saveBtnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL LIHAT DETAIL CATATAN ==================== -->
    <div id="detailModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-detail" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" onclick="closeDetailModal()"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 text-left shadow-2xl border border-slate-200 space-y-5 animate-scale-up" onclick="event.stopPropagation()">
                
                <!-- Header with Color Pill -->
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="space-y-1.5 flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span id="detailColorBadge" class="w-3 h-3 rounded-full shrink-0"></span>
                            <span id="detailDateRange" class="text-xs font-bold text-slate-500 font-mono"></span>
                        </div>
                        <h3 id="detailTitle" class="text-lg font-black text-slate-900 leading-snug break-words"></h3>
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Box Catatan Detail -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Isi Catatan Kegiatan:</span>
                    </label>
                    <div id="detailDescriptionBox" class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line min-h-[70px]">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Action Buttons: Tutup, Edit, Hapus -->
                <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="deleteCurrentEvent()" 
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 border border-rose-200 text-xs font-bold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus</span>
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeDetailModal()" 
                            class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition">
                            Tutup
                        </button>
                        <button type="button" onclick="editCurrentEvent()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Catatan</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script FullCalendar & Moment --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/id.js"></script>
    
    <script type="text/javascript">
        var SITEURL = "{{ url('/') }}";
        var calendarInstance = null;
        var activeEventObject = null;

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
              
            if ($('#calendar').length && typeof $.fn.fullCalendar === 'function') {
                calendarInstance = $('#calendar').fullCalendar({
                    editable: true,
                    events: SITEURL + "/guru/fullcalender",
                    displayEventTime: false,
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        day: 'Hari'
                    },
                eventRender: function (event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;
                    } else {
                        event.allDay = false;
                    }

                    // Terapkan warna kustom pada background & border
                    var eventColor = event.color || '#4f46e5';
                    element.css({
                        'background-color': eventColor,
                        'border-color': eventColor,
                        'color': '#ffffff'
                    });

                    // Tambahkan ikon dokumen catatan jika event memiliki isi catatan (description)
                    if (event.description && event.description.trim() !== '') {
                        var noteIcon = '<svg style="display:inline-block;width:11px;height:11px;margin-right:4px;vertical-align:-1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                        element.find('.fc-title').prepend(noteIcon);
                        element.attr('title', event.title + "\nCatatan: " + event.description);
                    } else {
                        element.attr('title', event.title);
                    }
                },
                selectable: true,
                selectHelper: true,
                select: function (start, end, allDay) {
                    var startFormat = $.fullCalendar.formatDate(start, "Y-MM-DD");
                    // FullCalendar end date is exclusive in all-day mode, subtract 1 day if multi-day
                    var adjustedEnd = moment(end).subtract(1, 'days');
                    var endFormat = adjustedEnd.isBefore(moment(start)) ? startFormat : adjustedEnd.format("YYYY-MM-DD");

                    openCreateModal(startFormat, endFormat);
                    calendarInstance.fullCalendar('unselect');
                },
                eventDrop: function (event, delta) {
                    var startFormat = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                    var endFormat = event.end ? $.fullCalendar.formatDate(event.end, "Y-MM-DD") : startFormat;

                    $.ajax({
                        url: SITEURL + '/guru/fullcalenderAjax',
                        data: {
                            id: event.id,
                            title: event.title,
                            description: event.description || '',
                            color: event.color || '#4f46e5',
                            start: startFormat,
                            end: endFormat,
                            type: 'update'
                        },
                        type: "POST",
                        success: function (response) {
                            toastr.success("Jadwal catatan berhasil dipindahkan", "Berhasil");
                        },
                        error: function () {
                            toastr.error("Gagal mengubah tanggal kegiatan", "Kesalahan");
                        }
                    });
                },
                eventClick: function (event) {
                    openDetailModal(event);
                }
            });
        }
    });

        // ==================== FUNGSI MODAL TAMBAH & EDIT ====================

        function selectColor(hex, labelText) {
            $('#event_color').val(hex);
            $('#selectedColorLabel').text(labelText).css('color', hex);
            $('#modalIconContainer').css('background-color', hex + '20').css('color', hex);

            $('.color-btn').each(function () {
                var btnColor = $(this).data('color');
                if (btnColor === hex) {
                    $(this).addClass('ring-2 ring-offset-2 ring-indigo-500 scale-110');
                    $(this).find('.check-icon').removeClass('hidden');
                } else {
                    $(this).removeClass('ring-2 ring-offset-2 ring-indigo-500 scale-110');
                    $(this).find('.check-icon').addClass('hidden');
                }
            });
        }

        function selectCustomColor(hex) {
            selectColor(hex, 'Kustom (' + hex + ')');
        }

        function openCreateModal(startDate, endDate) {
            var today = moment().format('YYYY-MM-DD');
            var sDate = startDate || today;
            var eDate = endDate || sDate;

            $('#event_id').val('');
            $('#event_title').val('');
            $('#event_description').val('');
            $('#event_start').val(sDate);
            $('#event_end').val(eDate);
            $('#modalTitle').text('Tambah Catatan Kegiatan');
            $('#saveBtnText').text('Simpan Catatan');

            selectColor('#4f46e5', 'Indigo (Default)');
            $('#eventModal').removeClass('hidden');
            setTimeout(function() { $('#event_title').focus(); }, 150);
        }

        function closeEventModal() {
            $('#eventModal').addClass('hidden');
            $('#eventForm')[0].reset();
        }

        function saveEvent() {
            var id = $('#event_id').val();
            var title = $.trim($('#event_title').val());
            var description = $.trim($('#event_description').val());
            var color = $('#event_color').val() || '#4f46e5';
            var start = $('#event_start').val();
            var end = $('#event_end').val();

            if (!title) {
                toastr.warning("Mohon isi judul kegiatan/catatan", "Perhatian");
                $('#event_title').focus();
                return;
            }
            if (!start) {
                toastr.warning("Mohon pilih tanggal mulai", "Perhatian");
                return;
            }
            if (!end) {
                end = start;
            }
            if (moment(end).isBefore(moment(start))) {
                toastr.warning("Tanggal selesai tidak boleh sebelum tanggal mulai", "Perhatian");
                return;
            }

            var type = id ? 'update' : 'add';

            // Loading state
            $('#saveEventBtn').prop('disabled', true);
            $('#saveBtnSpinner').removeClass('hidden');

            $.ajax({
                url: SITEURL + "/guru/fullcalenderAjax",
                type: "POST",
                data: {
                    id: id,
                    title: title,
                    description: description,
                    color: color,
                    start: start,
                    end: end,
                    type: type
                },
                success: function (data) {
                    closeEventModal();
                    calendarInstance.fullCalendar('refetchEvents');
                    toastr.success(id ? "Catatan berhasil diperbarui" : "Catatan kegiatan berhasil disimpan", "Berhasil");
                },
                error: function (xhr) {
                    toastr.error("Gagal menyimpan kegiatan. Silakan coba lagi.", "Kesalahan");
                },
                complete: function () {
                    $('#saveEventBtn').prop('disabled', false);
                    $('#saveBtnSpinner').addClass('hidden');
                }
            });
        }

        // ==================== FUNGSI MODAL DETAIL ====================

        function openDetailModal(event) {
            activeEventObject = event;

            $('#detailTitle').text(event.title);
            var color = event.color || '#4f46e5';
            $('#detailColorBadge').css('background-color', color);

            // Format tanggal
            var startStr = moment(event.start).format('DD MMM YYYY');
            var endStr = event.end ? moment(event.end).subtract(event.allDay ? 1 : 0, 'days').format('DD MMM YYYY') : startStr;
            var rangeText = (startStr === endStr) ? startStr : (startStr + ' - ' + endStr);
            $('#detailDateRange').text(rangeText);

            // Isi Catatan
            if (event.description && event.description.trim() !== '') {
                $('#detailDescriptionBox').text(event.description).removeClass('text-slate-400 italic');
            } else {
                $('#detailDescriptionBox').text('Tidak ada catatan keterangan tambahan.').addClass('text-slate-400 italic');
            }

            $('#detailModal').removeClass('hidden');
        }

        function closeDetailModal() {
            $('#detailModal').addClass('hidden');
            activeEventObject = null;
        }

        function editCurrentEvent() {
            if (!activeEventObject) return;
            var event = activeEventObject;
            closeDetailModal();

            var startFormat = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
            var endFormat = event.end ? $.fullCalendar.formatDate(event.end, "Y-MM-DD") : startFormat;

            $('#event_id').val(event.id);
            $('#event_title').val(event.title);
            $('#event_description').val(event.description || '');
            $('#event_start').val(startFormat);
            $('#event_end').val(endFormat);
            $('#modalTitle').text('Edit Catatan Kegiatan');
            $('#saveBtnText').text('Simpan Perubahan');

            var color = event.color || '#4f46e5';
            selectColor(color, 'Warna Pilihan (' + color + ')');

            $('#eventModal').removeClass('hidden');
            setTimeout(function() { $('#event_title').focus(); }, 150);
        }

        function deleteCurrentEvent() {
            if (!activeEventObject) return;
            var event = activeEventObject;

            if (confirm("Apakah Anda yakin ingin menghapus catatan kegiatan \"" + event.title + "\"?")) {
                $.ajax({
                    type: "POST",
                    url: SITEURL + '/guru/fullcalenderAjax',
                    data: {
                        id: event.id,
                        type: 'delete'
                    },
                    success: function (response) {
                        calendarInstance.fullCalendar('removeEvents', event.id);
                        closeDetailModal();
                        toastr.success("Kegiatan berhasil dihapus", "Berhasil");
                    },
                    error: function () {
                        toastr.error("Gagal menghapus kegiatan", "Kesalahan");
                    }
                });
            }
        }
    </script>
</x-app-layout>