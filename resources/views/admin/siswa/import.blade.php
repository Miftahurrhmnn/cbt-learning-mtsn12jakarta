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
                    <span class="text-xs font-bold text-slate-800">Import Data Siswa</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                        AD
                    </span>
                </div>
            </header>

            <!-- Desktop Sticky Sub-Top Header -->
            <header class="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition">Dashboard</a>
                    <span class="text-xs text-slate-300">/</span>
                    <a href="{{ route('admin.siswa.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition">Data Siswa</a>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">Import Excel & CSV</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-5xl w-full mx-auto">

                <!-- Page Header Title & CTA -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                Fitur Cepat
                            </span>
                            <span class="text-xs text-slate-400 font-medium">Khusus Administrator</span>
                        </div>
                        <h1 class="font-black text-2xl text-slate-900 leading-tight mt-1">
                            {{ __('Import Data Siswa (Excel & CSV)') }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah file spreadsheet untuk menambahkan atau memperbarui data siswa secara massal dalam hitungan detik.</p>
                    </div>

                    <a href="{{ route('admin.siswa.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-2xs transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Kembali ke Data Siswa</span>
                    </a>
                </div>

                <!-- Alert Feedback -->
                @if(session('success'))
                    <div class="flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-xs">
                            <p class="font-bold">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl shadow-xs">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-xs">
                            <p class="font-bold">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-xs space-y-1">
                        <div class="flex items-center gap-2 font-bold text-xs text-rose-900">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Perhatian pada berkas:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Section 1: Download Template Cards -->
                <div class="bg-gradient-to-br from-slate-900 to-[#1B2430] rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-2 max-w-xl">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-blue-200 text-xs font-semibold backdrop-blur-xs">
                                <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Langkah 1: Unduh Format Baku</span>
                            </div>
                            <h2 class="text-lg sm:text-xl font-black text-white tracking-tight">
                                Unduh Template Master Siswa
                            </h2>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Gunakan template yang telah disiapkan di bawah ini agar susunan kolom, format NISN, dan data kelas sesuai otomatis dengan sistem database.
                            </p>
                        </div>

                        <!-- Template Buttons -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                            <!-- Tombol Download Excel -->
                            <a href="{{ route('admin.siswa.template', 'xlsx') }}" 
                               class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-900/30 transition duration-150 cursor-pointer">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm1.5 12.5l-1.5 2.5-1.5-2.5H11l2.25 3.5L11 21.5h1.5l1.5-2.5 1.5 2.5H17l-2.25-3.5L17 14.5h-1.5zM14 9V3.5L18.5 8H14z"/>
                                </svg>
                                <span>Download Excel (.xlsx)</span>
                            </a>

                            <!-- Tombol Download CSV -->
                            <a href="{{ route('admin.siswa.template', 'csv') }}" 
                               class="inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 active:bg-white/30 text-white font-bold text-xs border border-white/20 transition duration-150 cursor-pointer">
                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Download CSV (.csv)</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Upload File Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden" 
                     x-data="{ fileName: '', fileSize: '', isDragging: false }">
                    <div class="p-6 sm:p-8">
                        <div class="mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-2">
                                Langkah 2: Unggah Berkas
                            </span>
                            <h3 class="text-base font-extrabold text-slate-900">
                                Unggah File yang Sudah Diisi
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Pastikan file berekstensi <span class="font-bold text-slate-700">.xlsx, .xls</span> atau <span class="font-bold text-slate-700">.csv</span> dengan ukuran maksimal 5 MB.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.siswa.process-import') }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            <!-- Drag and Drop Dropzone -->
                            <div 
                                @dragover.prevent="isDragging = true" 
                                @dragleave.prevent="isDragging = false" 
                                @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length > 0) { $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0].name; fileSize = ($event.dataTransfer.files[0].size / 1024).toFixed(1) + ' KB'; }"
                                :class="isDragging ? 'border-blue-500 bg-blue-50/50' : 'border-slate-300 bg-slate-50/50 hover:bg-slate-50'"
                                class="relative border-2 border-dashed rounded-3xl p-8 sm:p-12 text-center transition-all cursor-pointer flex flex-col items-center justify-center">
                                
                                <input 
                                    x-ref="fileInput"
                                    type="file" 
                                    name="file" 
                                    id="file" 
                                    accept=".xlsx, .xls, .csv, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    @change="if ($event.target.files.length > 0) { fileName = $event.target.files[0].name; fileSize = ($event.target.files[0].size / 1024).toFixed(1) + ' KB'; }"
                                    required
                                >

                                <div class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4 shadow-xs">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-slate-800">
                                        <span class="text-blue-600 hover:underline">Klik untuk memilih file</span> atau seret file ke area ini
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Format didukung: Microsoft Excel (.xlsx, .xls) atau CSV (.csv)
                                    </p>
                                </div>

                                <!-- File Preview Pill saat file dipilih -->
                                <template x-if="fileName">
                                    <div class="mt-4 inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-white border border-blue-200 text-blue-900 shadow-xs">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-bold" x-text="fileName"></span>
                                        <span class="text-[10px] text-slate-400" x-text="'(' + fileSize + ')'"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Submit CTA Button -->
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <a href="{{ route('admin.siswa.index') }}" class="px-5 py-3 rounded-2xl text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition">
                                    Batal
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 hover:shadow-lg transition cursor-pointer">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span>Mulai Import Siswa</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Section 3: KETERANGAN & FORMAT INPUT EXCEL YANG SANGAT JELAS -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="p-6 sm:p-8 space-y-6">
                        
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold mb-2">
                                Panduan Pengisian
                            </span>
                            <h3 class="text-base font-extrabold text-slate-900">
                                Keterangan & Aturan Isi Kolom di File Excel/CSV
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Mohon cermati tabel format di bawah ini agar proses import berjalan sukses tanpa kendala:
                            </p>
                        </div>

                        <!-- Table Guidelines -->
                        <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-2xs">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100/80 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3.5">Nama Kolom Header</th>
                                        <th class="px-4 py-3.5">Sifat</th>
                                        <th class="px-4 py-3.5">Tipe Data</th>
                                        <th class="px-4 py-3.5">Contoh Isi</th>
                                        <th class="px-4 py-3.5">Keterangan & Logika Sistem</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                                    <!-- 1. Nama Lengkap -->
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            Nama Lengkap
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800">
                                                Wajib
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-slate-500">Teks</td>
                                        <td class="px-4 py-3.5 font-mono text-[11px] text-slate-800">Ahmad Fauzi</td>
                                        <td class="px-4 py-3.5 text-slate-600 leading-relaxed">
                                            Nama lengkap siswa yang akan tampil di lembar ujian, kartu ujian, dan daftar nilai.
                                        </td>
                                    </tr>

                                    <!-- 2. NISN -->
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            NISN
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">
                                                Disarankan
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-slate-500">Angka / Teks</td>
                                        <td class="px-4 py-3.5 font-mono text-[11px] text-slate-800">0081234567</td>
                                        <td class="px-4 py-3.5 text-slate-600 leading-relaxed">
                                            Nomor Induk Siswa Nasional (unik). Digunakan untuk identifikasi siswa, pencarian, dan pembuatan email/password otomatis jika kolom email/password dikosongkan.
                                        </td>
                                    </tr>

                                    <!-- 3. Kelas -->
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            Kelas
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">
                                                Disarankan
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-slate-500">Teks</td>
                                        <td class="px-4 py-3.5 font-mono text-[11px] text-slate-800">Kelas X-A</td>
                                        <td class="px-4 py-3.5 text-slate-600 leading-relaxed">
                                            Nama kelas siswa. <span class="font-bold text-emerald-700">Otomatis Dibuatkan:</span> Jika nama kelas belum terdaftar di database CBT, sistem akan secara otomatis mendaftarkannya sebagai kelas baru.
                                        </td>
                                    </tr>

                                    <!-- 4. Email -->
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            Email
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700">
                                                Opsional
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-slate-500">Email</td>
                                        <td class="px-4 py-3.5 font-mono text-[11px] text-slate-800">ahmad@siswa.mtsn12.sch.id</td>
                                        <td class="px-4 py-3.5 text-slate-600 leading-relaxed">
                                            Alamat email untuk login siswa. Jika dikosongkan, sistem otomatis membuatkan email: <span class="font-mono bg-slate-100 px-1 py-0.5 rounded text-[11px] text-slate-700">[NISN]@siswa.mtsn12.sch.id</span>.
                                        </td>
                                    </tr>

                                    <!-- 5. Password -->
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3.5 font-bold text-slate-900">
                                            Password
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700">
                                                Opsional
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-slate-500">Teks</td>
                                        <td class="px-4 py-3.5 font-mono text-[11px] text-slate-800">password123</td>
                                        <td class="px-4 py-3.5 text-slate-600 leading-relaxed">
                                            Kata sandi login siswa (otomatis di-hash enkripsi bcrypt). Jika dikosongkan, sistem menggunakan NISN sebagai password default (atau kata <code class="font-mono text-slate-700">password</code> jika NISN kosong).
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Info Callouts / Tips -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                            <!-- Tip 1 -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                                <div class="flex items-center gap-2 text-slate-800 font-bold text-xs">
                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Pembaruan Otomatis</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Jika siswa dengan NISN atau Email yang sama sudah ada di sistem, data namanya dan kelasnya akan otomatis diperbarui tanpa membuat duplikat akun.
                                </p>
                            </div>

                            <!-- Tip 2 -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                                <div class="flex items-center gap-2 text-slate-800 font-bold text-xs">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Format NISN Angka 0</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Di Excel, format kolom NISN sebagai <strong>Text</strong> (bukan Number) agar angka nol di awal (misal: <code class="font-mono">008...</code>) tidak hilang otomatis oleh Excel.
                                </p>
                            </div>

                            <!-- Tip 3 -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                                <div class="flex items-center gap-2 text-slate-800 font-bold text-xs">
                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Keamanan Password</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Semua kata sandi yang diimport dienkripsi aman dengan algoritma bcrypt. Siswa dapat mengganti kata sandi mereka secara mandiri setelah login ke portal.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </main>
        </div>
    </div>
</x-app-layout>
