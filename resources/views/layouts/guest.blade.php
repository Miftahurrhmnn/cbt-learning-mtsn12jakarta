<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CBT Simpel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased min-h-full bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-x-hidden">
        <!-- Background Ambient Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-gradient-to-b from-indigo-100/50 via-purple-50/30 to-transparent blur-3xl pointer-events-none"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="text-center">
                <a href="/" class="inline-flex items-center space-x-2.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white font-black flex items-center justify-center text-lg shadow-md group-hover:scale-105 transition-transform duration-200">
                        CBT
                    </div>
                    <span class="font-black text-2xl text-slate-900 tracking-tight">
                        CBT <span class="text-indigo-600">Learning</span>
                    </span>
                </a>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0 relative z-10">
            <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-200/70">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} CBT Simpel. Platform Ujian Online Interaktif.
            </p>
        </div>
    </body>
</html>
