<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .prose-content {
                font-family: 'Source Serif Pro', serif;
            }
            body {
                font-family: 'Inter', sans-serif;
            }
            .writing-bg {
                background-color: #f8f9fa;
                background-image: linear-gradient(to right, rgba(255,255,255,0.8) 1px, transparent 1px),
                                linear-gradient(to bottom, rgba(255,255,255,0.8) 1px, transparent 1px);
                background-size: 20px 20px;
            }
            .dark .writing-bg {
                background-color: #1a1a1a;
                background-image: linear-gradient(to right, rgba(255,255,255,0.1) 1px, transparent 1px),
                                linear-gradient(to bottom, rgba(255,255,255,0.1) 1px, transparent 1px);
            }
        </style>

        <script>
            // Verifica se o modo escuro está ativado no localStorage
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }

            // Observa mudanças no modo escuro
            window.addEventListener('dark-mode-changed', function(e) {
                if (e.detail.darkMode) {
                    localStorage.theme = 'dark'
                } else {
                    localStorage.theme = 'light'
                }
            })
        </script>
    </head>
    <body class="font-sans antialiased h-full">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @if (session('success'))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-100 py-4 mt-8 dark:bg-gray-800/80 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        Desenvolvido para escritores e redatores criativos
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>
