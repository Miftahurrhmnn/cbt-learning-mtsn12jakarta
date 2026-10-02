<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Masuk - CBT Learning</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap"
        rel="stylesheet"
    >

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="relative flex min-h-full flex-col justify-between bg-[#f8fafc] font-sans text-slate-900 antialiased selection:bg-indigo-600 selection:text-white"
>

    <!-- =========================================================
         BACKGROUND
    ========================================================== -->
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div
            class="absolute -left-32 -top-32 h-[500px] w-[500px] rounded-full bg-indigo-100/70 blur-3xl"
        ></div>
        <div
            class="absolute -right-40 top-[20%] h-[450px] w-[450px] rounded-full bg-violet-100/50 blur-3xl"
        ></div>
        <div
            class="absolute bottom-0 left-1/2 h-[300px] w-[700px] -translate-x-1/2 rounded-full bg-blue-50/60 blur-3xl"
        ></div>
    </div>

    <!-- =========================================================
         MAIN CONTENT - LOGIN FORM
    ========================================================== -->
    <main class="flex flex-1 items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        
        <div class="w-full max-w-md">
            
            <!-- Login Card -->
            <div class="overflow-hidden rounded-[28px] border border-slate-200/80 bg-white/90 p-8 shadow-xl shadow-slate-200/60 backdrop-blur-xl sm:p-10">
                
                <!-- Logo & Header -->
                <div class="text-center">
                    <div class="flex justify-center items-center">
                        <img
                            src="{{ asset('images/favicon.ico') }}"
                            alt="Logo"
                            class="h-9 w-9 object-contain"
                        />
                    </div>
                    
                    <h1 class="mt-4 text-xl font-extrabold tracking-tight text-slate-950 sm:text-2xl">
                        Masuk ke Sistem
                    </h1>
                    
                    <p class="mt-1.5 text-xs text-slate-500 sm:text-sm">
                        Masukkan akun Anda untuk melanjutkan ujian
                    </p>
                </div>

                <!-- Session Status / Error Alert -->
                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-xs text-red-600">
                        <p class="font-bold">Gagal Masuk:</p>
                        <ul class="mt-1 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Email
                        </label>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            required 
                            autofocus
                            value="{{ old('email') }}"
                            placeholder="Masukkan akun Anda"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-600/20"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Password
                            </label>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            placeholder="••••••••"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-600/20"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Masuk Sekarang
                    </button>
                </form>
                
            </div>
        </div>
    </main>


    <!-- =========================================================
         FOOTER
    ========================================================== -->
    <footer class="w-full py-6 text-center">
        <p class="text-[11px] font-medium text-slate-400">
            &copy; {{ date('Y') }} CBT Learning. All rights reserved.
        </p>
    </footer>

</body>
</html>