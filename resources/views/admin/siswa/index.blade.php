<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-black uppercase tracking-wider bg-rose-100 text-rose-800">
                        🛡️ Portal Administrator
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-semibold text-slate-500">Master Data</span>
                </div>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">
                    {{ __('Manajemen Data Siswa') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Khusus akun Administrator. Guru dan Siswa tidak memiliki izin untuk menginput atau mengelola data ini.</p>
            </div>
            
            <a href="{{ route('admin.siswa.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-indigo-100 transition hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tambah Siswa Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alerts -->
            @if(session('success'))
                <div class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Filter & Search Bar -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <form method="GET" action="{{ route('admin.siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-6">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NISN, atau Email..."
                                   class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <select name="classroom_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classrooms as $cls)
                                <option value="{{ $cls->id }}" {{ request('classroom_id') == $cls->id ? 'selected' : '' }}>
                                    {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'classroom_id']))
                            <a href="{{ route('admin.siswa.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold" title="Reset Filter">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-800">
                        Daftar Siswa Terdaftar (Total: {{ $students->total() }})
                    </h3>
                </div>

                @if($students->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h4 class="font-bold text-sm text-slate-800">Belum Ada Data Siswa</h4>
                        <p class="text-xs text-slate-500 mt-1">Gunakan tombol Tambah Siswa Baru di atas untuk menginput akun siswa.</p>
                    </div>
                @else
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/80 text-left">
                                <tr>
                                    <th class="px-6 py-3.5 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Nama & Email</th>
                                    <th class="px-6 py-3.5 text-xs font-extrabold text-slate-500 uppercase tracking-wider">NISN</th>
                                    <th class="px-6 py-3.5 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-3.5 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Terdaftar Sejak</th>
                                    <th class="px-6 py-3.5 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($students as $st)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 font-black text-xs flex items-center justify-center">
                                                    {{ strtoupper(substr($st->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-sm text-slate-900">{{ $st->name }}</div>
                                                    <div class="text-xs text-slate-500 font-mono">{{ $st->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-semibold text-slate-700">
                                            {{ $st->nisn ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($st->classroom)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                                    {{ $st->classroom->name }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Belum diatur</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                            {{ $st->created_at ? $st->created_at->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold">
                                            <div class="inline-flex items-center gap-1.5">
                                                <a href="{{ route('admin.siswa.edit', $st->id) }}" 
                                                   class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.siswa.destroy', $st->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus siswa {{ $st->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards List -->
                    <div class="block md:hidden divide-y divide-slate-100">
                        @foreach($students as $st)
                            <div class="p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 font-black text-xs flex items-center justify-center">
                                            {{ strtoupper(substr($st->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-slate-900">{{ $st->name }}</h4>
                                            <p class="text-xs text-slate-500 font-mono">{{ $st->email }}</p>
                                        </div>
                                    </div>
                                    @if($st->classroom)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700">
                                            {{ $st->classroom->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
                                    <span>NISN: <strong class="text-slate-700">{{ $st->nisn ?? '-' }}</strong></span>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.siswa.edit', $st->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.siswa.destroy', $st->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus siswa {{ $st->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 font-bold">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 border-t border-slate-100">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
