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

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-full flex flex-col selection:bg-indigo-500 selection:text-white">
        @if($hideNav ?? false)
            <div class="min-h-screen flex flex-col flex-1">
                {{ $slot }}
            </div>
        @else
            <div class="min-h-screen flex flex-col flex-1">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-b border-slate-200/80 shadow-xs">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>

                <!-- Modern Footer -->
                <footer class="bg-white border-t border-slate-200/70 py-6 text-center text-xs text-slate-400">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
                        <p>&copy; {{ date('Y') }} CBT MTsN 12 Jakarta.</p>
                    </div>
                </footer>
            </div>
        @endif
    </body>
</html>
