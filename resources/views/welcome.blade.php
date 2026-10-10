<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Log in to the site | CBT MTsN 12 Jakarta</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        
        /* Background kampus dengan visual arsitektur modern */
        .campus-bg {
            background-color: #1e293b;
            background-image: 
                linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.45)),
                url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        /* Diagonal Stripe Decorative Ribbons */
        .ribbon-diagonal {
            transform: rotate(-38deg);
            transform-origin: center;
        }
    </style>
</head>

<body class="min-h-full font-sans antialiased text-slate-800 campus-bg relative flex flex-col justify-center items-center p-4 sm:p-6 overflow-x-hidden selection:bg-emerald-600 selection:text-white">

    <!-- =========================================================
         DECORATIVE DIAGONAL COLORFUL STRIPES (ACCENTS CORNERS)
         Sesuai referensi: Garis diagonal warna hijau, kuning, navy & dot
    ========================================================== -->
    
    <!-- Top-Left Decorative Diagonal Ribbons -->
    <div class="pointer-events-none fixed -top-24 -left-28 sm:-top-20 sm:-left-20 w-80 sm:w-96 h-80 sm:h-96 z-0 overflow-visible opacity-95">
        <div class="relative w-full h-full ribbon-diagonal">
            <!-- Navy Blue Stripe -->
            <div class="absolute top-2 left-0 w-80 sm:w-96 h-10 sm:h-12 bg-[#1b3a6b] rounded-full shadow-lg"></div>
            <!-- Green Emerald Stripe -->
            <div class="absolute top-16 left-6 w-72 sm:w-88 h-9 sm:h-11 bg-[#2ea44f] rounded-full shadow-md"></div>
            <!-- Yellow Amber Stripe -->
            <div class="absolute top-30 left-12 w-64 sm:w-80 h-8 sm:h-10 bg-[#f59e0b] rounded-full shadow-md"></div>
            <!-- Accent Dots -->
            <div class="absolute top-2 left-88 w-6 h-6 rounded-full bg-[#2ea44f]"></div>
            <div class="absolute top-20 left-84 w-8 h-8 rounded-full bg-[#f59e0b]"></div>
            <div class="absolute top-44 left-16 w-7 h-7 rounded-full bg-[#1b3a6b]"></div>
            <div class="absolute top-52 left-32 w-5 h-5 rounded-full bg-[#2ea44f]"></div>
        </div>
    </div>

    <!-- Bottom-Right Decorative Diagonal Ribbons -->
    <div class="pointer-events-none fixed -bottom-24 -right-28 sm:-bottom-20 sm:-right-20 w-80 sm:w-96 h-80 sm:h-96 z-0 overflow-visible opacity-95">
        <div class="relative w-full h-full ribbon-diagonal">
            <!-- Green Emerald Stripe -->
            <div class="absolute bottom-24 right-0 w-80 sm:w-96 h-10 sm:h-12 bg-[#2ea44f] rounded-full shadow-lg"></div>
            <!-- Yellow Amber Stripe -->
            <div class="absolute bottom-10 right-6 w-72 sm:w-88 h-9 sm:h-11 bg-[#f59e0b] rounded-full shadow-md"></div>
            <!-- Navy Blue Stripe -->
            <div class="absolute -bottom-4 right-12 w-64 sm:w-80 h-8 sm:h-10 bg-[#1b3a6b] rounded-full shadow-md"></div>
            <!-- Accent Dots -->
            <div class="absolute bottom-40 right-14 w-8 h-8 rounded-full bg-[#2ea44f]"></div>
            <div class="absolute bottom-48 right-32 w-5 h-5 rounded-full bg-[#f59e0b]"></div>
            <div class="absolute bottom-20 -right-8 w-6 h-6 rounded-full bg-[#1b3a6b]"></div>
        </div>
    </div>

    <!-- =========================================================
         MAIN LOGIN CARD (CENTERED & FULLY RESPONSIVE)
    ========================================================== -->
    <main class="relative z-10 w-full max-w-[440px] sm:max-w-[460px] my-auto">
        
        <!-- White Card with subtle elevation shadow -->
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl p-6 sm:p-9 border border-slate-200/80 transition-all duration-200">
            
            <!-- Banner Logo Header (Mirip Banner Mercu Buana / FAST Learning) -->
            <div class="rounded-xl border border-slate-200/90 bg-gradient-to-b from-slate-50/90 to-white p-5 sm:p-6 mb-6 text-center shadow-2xs">
                
                <div class="flex items-center justify-center gap-3.5 mb-2.5">
                    <!-- School Emblem / Icon -->
                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200/80 p-1.5 flex items-center justify-center shadow-xs shrink-0">
                        <img 
                            src="{{ asset('images/favicon.ico') }}" 
                            alt="Logo MTsN 12 Jakarta" 
                            class="w-full h-full object-contain"
                            onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'%231b3a6b\' viewBox=\'0 0 24 24\'><path d=\'M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z\'/></svg>';"
                        />
                    </div>

                    <!-- Divider -->
                    <div class="h-10 w-[1.5px] bg-slate-200"></div>

                    <!-- CBT System Badge Graphic -->
                    <div class="text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-[#1b3a6b] text-white">
                                CBT
                            </span>
                            <span class="text-xs font-black tracking-tight text-[#2ea44f] uppercase">
                                LEARNING
                            </span>
                        </div>
                        <p class="text-[10px] font-semibold text-slate-400 mt-0.5 tracking-tight">
                            MTsN 12 JAKARTA
                        </p>
                    </div>
                </div>

                <!-- Title & Subtitle -->
                <h1 class="text-xs sm:text-sm font-extrabold text-slate-800 tracking-tight">
                    CBT MTsN 12 Jakarta
                </h1>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                    Computer Based Test & Evaluasi Belajar Siswa
                </p>
            </div>

            <!-- Error Notification Alert -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs shadow-xs animate-shake">
                    <div class="flex items-center gap-2 font-bold mb-1 text-rose-800">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Gagal Masuk:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            <!-- =========================================================
                 FORM LOGIN UTAMA (PERSIS FORMAT INPUT REFERENSI)
            ========================================================== -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
                @csrf

                <!-- Input Username / Email dengan Icon Orang di Kiri -->
                <div class="relative flex items-center rounded-lg border border-slate-300 bg-white transition-all duration-150 focus-within:border-[#2ea44f] focus-within:ring-2 focus-within:ring-[#2ea44f]/20">
                    <div class="pl-3.5 pr-2.5 flex items-center pointer-events-none text-slate-400">
                        <!-- Icon User / Person Sesuai Screenshot -->
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="email" 
                        id="email"
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        placeholder="Username / Email"
                        class="w-full py-3 pr-3.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-transparent border-0 focus:ring-0 focus:outline-none"
                    >
                </div>

                <!-- Input Password dengan Icon Kunci di Kiri & Icon Mata di Kanan -->
                <div class="relative flex items-center rounded-lg border border-slate-300 bg-white transition-all duration-150 focus-within:border-[#2ea44f] focus-within:ring-2 focus-within:ring-[#2ea44f]/20">
                    <div class="pl-3.5 pr-2.5 flex items-center pointer-events-none text-[#7ba4cc]">
                        <!-- Icon Kunci Sesuai Screenshot -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        name="password" 
                        id="password"
                        required 
                        placeholder="••••••••••••"
                        class="w-full py-3 pr-10 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-transparent border-0 focus:ring-0 focus:outline-none"
                    >

                    <!-- Tombol Lihat / Sembunyikan Password dengan Icon Mata di Kotak Abu -->
                    <button 
                        type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute right-2.5 p-1.5 rounded-md bg-slate-100/90 hover:bg-slate-200 text-slate-500 hover:text-slate-800 transition"
                        aria-label="Tampilkan atau sembunyikan password"
                        tabindex="-1"
                    >
                        <!-- Icon Mata Normal (Password Tersembunyi) -->
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Icon Mata Dicoret (Password Terlihat) -->
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                <!-- Tombol Submit "Log in" Berwarna Hijau Solid Sesuai Desain Referensi -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-lg bg-[#5fa835] hover:bg-[#52932e] active:bg-[#478227] text-white text-sm font-bold tracking-wide shadow-md shadow-[#5fa835]/25 hover:shadow-lg transition-all duration-150 flex items-center justify-center cursor-pointer"
                    >
                        Log in
                    </button>
                </div>

            </form>

        </div>

        <!-- Copyright Subtle Note -->
        <div class="mt-4 text-center">
            <p class="text-[11px] text-white/70 font-medium drop-shadow-xs">
                &copy; {{ date('Y') }} MTsN 12 Jakarta. All rights reserved. | Support by M1FDev
            </p>
        </div>

    </main>

</body>
</html>