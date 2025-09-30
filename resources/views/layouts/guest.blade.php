<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preload" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" as="font" type="font/woff2" crossorigin="anonymous">
        <link rel="preload" href="{{ logo() }}" as="image">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .logo {
                max-width: 200px;
            }
            .auth-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            .auth-bg-dark {
                background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            }
            .glass-effect {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .glass-effect-dark {
                backdrop-filter: blur(10px);
                background: rgba(0, 0, 0, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen auth-bg dark:auth-bg-dark flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/20 to-transparent"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-white/10 to-transparent rounded-full transform translate-x-1/2 translate-y-1/2"></div>
                <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-br from-white/5 to-transparent rounded-full"></div>
            </div>

            <!-- Logo Section -->
            <div class="relative z-10 mb-8 animate-fade-in">
                <a href="/" class="block transform hover:scale-105 transition-transform duration-300">
                    <img class="logo mx-auto drop-shadow-lg" src="{{ logo() }}" alt="{{ config('app.name') }}" width="400" height="300">
                </a>
            </div>

            <!-- Auth Card -->
            <div class="relative z-10 w-full sm:max-w-md px-6 py-8 animate-slide-up">
                <div class="glass-effect dark:glass-effect-dark rounded-2xl shadow-2xl overflow-hidden">
                    <div class="px-8 py-10">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="relative z-10 mt-8 text-center animate-fade-in">
                <p class="text-white/80 text-sm">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
