<x-app-layout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 bg-slate-50 px-6 py-6 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                            class="h-7 w-7"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 5.25a3 3 0 1 1-5.9.75h-.6a2.25 2.25 0 0 0-2.25 2.25v7.5A2.25 2.25 0 0 0 9.25 18h5.5A2.25 2.25 0 0 0 17 15.75v-7.5a2.25 2.25 0 0 0-1.25-2.013M15.75 5.25V4.5a3 3 0 1 0-5.9.75m5.9 0H21m-5.25 0h-1.5"
                            />
                        </svg>
                    </div>

                    <h1 class="text-xl font-bold text-slate-900">
                        Masukkan Token Ujian
                    </h1>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Masukkan token yang diberikan oleh guru untuk memulai ujian.
                    </p>
                </div>

                <div class="p-6 sm:p-7">
                    <div class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Ujian
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $exam->display_name }}
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                            <span>
                                {{ $exam->subject->name ?? '-' }}
                            </span>

                            <span>•</span>

                            <span>
                                {{ $exam->classroom->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('siswa.ujian.verify-token', $exam->id) }}"
                        class="mt-4"
                    >
                        @csrf

                        <div>
                            <label
                                for="token"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Token Ujian
                            </label>

                            <input
                                id="token"
                                name="token"
                                type="text"
                                value="{{ old('token') }}"
                                minlength="7"
                                maxlength="7"
                                required
                                autocomplete="off"
                                autofocus
                                placeholder="Contoh: MTK2026"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 7)"
                                class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-center font-mono text-lg font-bold uppercase tracking-[0.3em] shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            <p class="mt-2 text-xs text-slate-400">
                                Token terdiri dari tepat 7 karakter huruf dan angka.
                            </p>

                            @error('token')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Masuk ke Ujian
                        </button>
                    </form>

                    <a
                        href="{{ route('siswa.dashboard') }}"
                        class="mt-4 block text-center text-sm font-medium text-slate-500 hover:text-slate-700"
                    >
                        Kembali ke Dashboard
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>