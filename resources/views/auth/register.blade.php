<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-extrabold text-slate-900">Daftar Akun Baru</h2>
        <p class="text-xs text-slate-500 mt-1">Pilih peran Anda dan lengkapi data untuk mulai</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ selectedRole: '{{ old('role', 'siswa') }}' }">
        @csrf

        <!-- Pilihan Peran (Role Selector) Interaktif -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Daftar Sebagai:</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col items-center text-center group active:scale-[0.98]"
                    :class="selectedRole === 'siswa' ? 'border-[#2B77DE] bg-blue-50/80 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                    <input type="radio" name="role" value="siswa" class="sr-only" x-model="selectedRole">
                    <div class="w-10 h-10 rounded-xl mb-1.5 flex items-center justify-center transition"
                        :class="selectedRole === 'siswa' ? 'bg-[#2B77DE] text-white shadow-xs' : 'bg-slate-100 text-slate-500 group-hover:text-slate-700'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5"/></svg>
                    </div>
                    <span class="text-xs font-extrabold text-slate-900">Siswa</span>
                    <span class="text-[10px] text-slate-500 mt-0.5">Peserta Ujian CBT</span>
                </label>

                <label class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col items-center text-center group active:scale-[0.98]"
                    :class="selectedRole === 'guru' ? 'border-indigo-600 bg-indigo-50/80 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                    <input type="radio" name="role" value="guru" class="sr-only" x-model="selectedRole">
                    <div class="w-10 h-10 rounded-xl mb-1.5 flex items-center justify-center transition"
                        :class="selectedRole === 'guru' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-500 group-hover:text-slate-700'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <span class="text-xs font-extrabold text-slate-900">Guru</span>
                    <span class="text-[10px] text-slate-500 mt-0.5">Pembuat Soal & Ujian</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                placeholder="Contoh: Ahmad Fauzi atau Budi Santoso, S.Pd."
                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                placeholder="nama@email.com"
                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Ulangi password"
                class="block w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition shadow-2xs">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl text-sm font-bold uppercase tracking-wider shadow-sm transition duration-150 hover:shadow-md">
                Daftar & Masuk &rarr;
            </button>
        </div>
    </form>

    <div class="mt-6 text-center text-xs text-slate-500 border-t border-slate-100 pt-5">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800">
            Masuk di Sini
        </a>
    </div>
</x-guest-layout>
