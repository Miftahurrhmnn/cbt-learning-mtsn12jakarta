<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card Informasi Kebijakan Nilai -->
            <div class="p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-900 rounded-r shadow-xs text-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span><strong>Aturan Penilaian:</strong> Sesuai ketentuan sistem CBT, nilai siswa <em>hanya akan keluar dan dihitung</em> apabila siswa telah menyelesaikan seluruh proses ujian. Jika siswa belum menyelesaikan ujian, nilai tidak akan ditampilkan di sistem Guru maupun Siswa.</span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Pengerjaan Siswa ({{ $sessions->count() }} Siswa)</h3>

                    @if($sessions->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <h4 class="mt-2 text-sm font-semibold text-gray-900">Belum ada siswa yang mengerjakan</h4>
                            <p class="mt-1 text-sm text-gray-500">Pastikan ujian telah berstatus 'Aktif' agar siswa dapat mulai mengerjakan.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jawaban Benar</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($sessions as $index => $session)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-semibold text-gray-900 text-sm">{{ $session->user->name ?? 'Siswa' }}</div>
                                                <div class="text-xs text-gray-500">{{ $session->user->email ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                                {{ $session->start_time ? $session->start_time->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                                {{ $session->end_time ? $session->end_time->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($session->isCompleted())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        ✓ Selesai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                        Belum Selesai (Sedang Dikerjakan)
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-700">
                                                @if($session->isCompleted())
                                                    {{ $session->correct_answers }} / {{ $session->total_questions }} Soal
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                @if($session->isCompleted())
                                                    <span class="text-lg font-extrabold {{ $session->score >= 75 ? 'text-emerald-600' : 'text-indigo-600' }}">
                                                        {{ number_format($session->score, 1) }}
                                                    </span>
                                                    <span class="text-xs text-gray-400 font-normal">/ 100</span>
                                                @else
                                                    <span class="text-xs text-rose-500 italic bg-rose-50 px-2 py-1 rounded">
                                                        Nilai Belum Keluar
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
