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

        <!-- Toastr Notification CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

        <!-- jQuery & Toastr JS (Loaded in head so view scripts can access without re-overwriting) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }

            /* Modern Toastr Popup Styling (Pojok Kanan Atas) */
            #toast-container {
                top: 20px !important;
                right: 20px !important;
                z-index: 999999 !important;
            }
            #toast-container > div {
                opacity: 0.98 !important;
                border-radius: 1rem !important;
                box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1) !important;
                padding: 16px 18px 16px 52px !important;
                font-family: inherit !important;
                border: 1px solid rgba(255, 255, 255, 0.2) !important;
                backdrop-filter: blur(8px) !important;
            }
            #toast-container > .toast-success {
                background-color: #059669 !important; /* emerald-600 */
            }
            #toast-container > .toast-error {
                background-color: #e11d48 !important; /* rose-600 */
            }
            #toast-container > .toast-warning {
                background-color: #d97706 !important; /* amber-600 */
            }
            #toast-container > .toast-info {
                background-color: #2563eb !important; /* blue-600 */
            }
            .toast-title {
                font-weight: 800 !important;
                font-size: 0.875rem !important;
                letter-spacing: -0.01em !important;
                margin-bottom: 3px !important;
            }
            .toast-message {
                font-size: 0.8125rem !important;
                font-weight: 500 !important;
                line-height: 1.4 !important;
                color: rgba(255, 255, 255, 0.95) !important;
            }
            .toast-close-button {
                font-size: 1.25rem !important;
                font-weight: 700 !important;
                color: #ffffff !important;
                opacity: 0.75 !important;
                text-shadow: none !important;
                right: -4px !important;
                top: -4px !important;
            }
            .toast-close-button:hover {
                opacity: 1 !important;
            }
            .toast-progress {
                background-color: rgba(255, 255, 255, 0.45) !important;
                height: 3px !important;
            }
            @media (max-width: 640px) {
                #toast-container {
                    top: 12px !important;
                    right: 12px !important;
                    left: 12px !important;
                }
                #toast-container > div {
                    width: 100% !important;
                }
            }
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
        
        <script>
            $(document).ready(function() {
                if (typeof toastr !== 'undefined') {
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "showDuration": "300",
                        "hideDuration": "800",
                        "timeOut": "4500",
                        "extendedTimeOut": "1500",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };

                    @if(session('success'))
                        toastr.success({!! json_encode(session('success')) !!}, 'Berhasil');
                    @endif

                    @if(session('error'))
                        toastr.error({!! json_encode(session('error')) !!}, 'Kesalahan');
                    @endif

                    @if(session('warning'))
                        toastr.warning({!! json_encode(session('warning')) !!}, 'Perhatian');
                    @endif

                    @if(session('info'))
                        toastr.info({!! json_encode(session('info')) !!}, 'Informasi');
                    @endif

                    @if(session('status') && !session('success'))
                        toastr.info({!! json_encode(session('status')) !!}, 'Status');
                    @endif

                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            toastr.error({!! json_encode($error) !!}, 'Validasi Gagal');
                        @endforeach
                    @endif
                }
            });
        </script>
    </body>
</html>
