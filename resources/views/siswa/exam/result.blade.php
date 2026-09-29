<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Hasil Ujian CBT') }}
            </h2>
            <a href="{{ route('siswa.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r shadow-xs">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(!$isCompleted)
                <!-- Tampilan Jika Ujian BELUM SELESAI (Nilai Tidak Keluar) -->
                <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-8 text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900">Ujian Belum Selesai</h3>
                    <p class="text-gray-600 max-w-md mx-auto text-sm leading-relaxed">
                        Nilai tidak dapat ditampilkan karena Anda belum menyelesaikan proses ujian ini secara tuntas.
                    </p>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-left text-xs space-y-1.5 max-w-md mx-auto text-gray-600">
                        <div><strong>Mata Pelajaran:</strong> {{ $exam->subject->name ?? '-' }}</div>
                        <div><strong>Kelas:</strong> {{ $exam->classroom->name ?? '-' }}</div>
                        <div><strong>Status Nilai:</strong> <span class="text-rose-500 font-semibold italic">Disembunyikan (Ujian Belum Selesai)</span></div>
                    </div>

                    <div class="pt-4 flex items-center justify-center gap-3">
                        <a href="{{ route('siswa.dashboard') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition">
                            Ke Dashboard
                        </a>
                        <a href="{{ route('siswa.ujian.show', $exam->id) }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow-xs transition">
                            Lanjutkan Ujian &rarr;
                        </a>
                    </div>
                </div>

            @else
                <!-- Tampilan Jika Ujian TELAH SELESAI (Nilai Ditampilkan) -->
                <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
                    <!-- Header Kartu Nilai -->
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-8 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white mb-3">
                            <x-heroicon-s-check class="h-5 w-5" /> Ujian Telah Selesai
                        </span>
                        <h3 class="text-2xl font-bold">{{ $exam->title ?? 'Ujian ' . $exam->subject->name }}</h3>
                        <p class="text-xs text-indigo-100 mt-1">{{ $exam->subject->name }} &bull; {{ $exam->classroom->name }}</p>

                        <!-- Angka Nilai -->
                        <div class="mt-6 inline-flex flex-col items-center justify-center w-36 h-36 rounded-full bg-white text-indigo-950 shadow-lg border-4 border-indigo-200">
                            <span class="text-4xl font-extrabold tracking-tight">
                                {{ number_format($score, 1) }}
                            </span>
                            <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider mt-0.5">Nilai Akhir</span>
                        </div>
                    </div>

                    <!-- Rincian Skor -->
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                                <span class="block text-2xl font-black text-emerald-600">
                                    {{ $session->correct_answers }}
                                </span>
                                <span class="text-xs text-gray-600 font-medium">Jawaban Benar</span>
                            </div>

                            <div class="p-4 bg-rose-50 rounded-xl border border-rose-100">
                                <span class="block text-2xl font-black text-rose-600">
                                    {{ $session->total_questions - $session->correct_answers }}
                                </span>
                                <span class="text-xs text-gray-600 font-medium">Jawaban Salah</span>
                            </div>

                            <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                                <span class="block text-2xl font-black text-indigo-600">
                                    {{ $session->total_questions }}
                                </span>
                                <span class="text-xs text-gray-600 font-medium">Total Soal</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-2 text-xs text-gray-500">
                            <div class="flex justify-between">
                                <span>Waktu Mulai:</span>
                                <strong class="text-gray-700">{{ $session->start_time ? $session->start_time->format('d M Y, H:i:s') : '-' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Waktu Selesai:</span>
                                <strong class="text-gray-700">{{ $session->end_time ? $session->end_time->format('d M Y, H:i:s') : '-' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Status Sistem:</span>
                                <strong class="text-emerald-600">Selesai & Tersimpan di Buku Nilai Guru</strong>
                            </div>
                        </div>

                        <div class="pt-4 text-center">
                            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow-xs transition">
                                <x-heroicon-s-arrow-left class="h-4 w-4" /> Kembali ke Dashboard Ujian
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
