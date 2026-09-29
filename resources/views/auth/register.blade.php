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
                <label class="p-3.5 rounded-2xl border-2 cursor-pointer transition flex flex-col items-center text-center"
                    :class="selectedRole === 'siswa' ? 'border-indigo-600 bg-indigo-50/70 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                    <input type="radio" name="role" value="siswa" class="sr-only" x-model="selectedRole">
                    <span class="text-2xl mb-1">👨‍🎓</span>
                    <span class="text-xs font-extrabold text-slate-900">Siswa</span>
                    <span class="text-[10px] text-slate-500 mt-0.5">Peserta Ujian CBT</span>
                </label>

                <label class="p-3.5 rounded-2xl border-2 cursor-pointer transition flex flex-col items-center text-center"
                    :class="selectedRole === 'guru' ? 'border-purple-600 bg-purple-50/70 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                    <input type="radio" name="role" value="guru" class="sr-only" x-model="selectedRole">
                    <span class="text-2xl mb-1">👨‍🏫</span>
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
