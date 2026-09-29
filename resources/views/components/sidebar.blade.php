<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 flex flex-col justify-between shadow-sm">
    
    <div>
        <!-- Brand Logo / Header Sidebar -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <img class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-black text-sm" src="{{ asset('images/favicon.ico') }}" alt="logo" />
                <span class="font-bold text-slate-800 text-base tracking-wide">MTsN 12 Jakarta</span>
            </div>
            <button @click="sidebarOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 md:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-1.5">
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wider rounded-xl bg-indigo-50 text-indigo-600">
                <x-heroicon-s-book-open class="w-5 h-5" />
                <span>Daftar Ujian</span>
            </a>

            <a href="{{ route('guru.ujian.create') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wider rounded-xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Ujian</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wider rounded-xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition">
                <x-heroicon-s-users class="w-5 h-5" />
                <span>Siswa & Kelas</span>
            </a>
        </nav>
    </div>

    <!-- Footer Sidebar / User Info -->
    <div class="p-4 border-t border-slate-100">
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-600 text-xs">
                BA
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name ?? 'Bapak Budi, S.Pd.' }}</p>
                <p class="text-[10px] text-slate-400">Guru Pengajar</p>
            </div>
        </div>
    </div>
</aside>