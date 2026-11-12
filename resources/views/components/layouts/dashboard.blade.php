@props([
    'title' => 'Taskora - Task Management System',
    'pageTitle' => 'Dashboard',
    'subtitle' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50" x-data="{ mobileMenuOpen: false }">
    <div class="min-h-screen flex bg-slate-50">
        {{-- Desktop Sidebar --}}
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0 z-30">
            <x-sidebar />
        </div>

        {{-- Mobile Sidebar Drawer & Overlay --}}
        <div
            x-show="mobileMenuOpen"
            class="relative z-50 lg:hidden"
            x-cloak
        >
            {{-- Backdrop --}}
            <div
                x-show="mobileMenuOpen"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
                @click="mobileMenuOpen = false"
            ></div>

            {{-- Off-canvas Menu --}}
            <div class="fixed inset-0 flex">
                <div
                    x-show="mobileMenuOpen"
                    x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    class="relative mr-16 flex w-full max-w-xs flex-1"
                >
                    <x-sidebar :mobile="true" class="w-full" />
                </div>
            </div>
        </div>

        {{-- Main Application Content Area --}}
        <div class="lg:pl-64 flex flex-col flex-1 min-w-0">
            {{-- Top Navbar --}}
            <x-navbar :title="$pageTitle" :subtitle="$subtitle" />

            {{-- Main Page Content --}}
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 sm:py-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>

            {{-- Application Footer --}}
            <footer class="border-t border-slate-200/60 bg-white/50 py-4 px-4 sm:px-6 lg:px-8 mt-auto">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                    <p>&copy; {{ date('Y') }} <span class="font-semibold text-slate-700">Taskora</span> &mdash; Organize Work. Achieve More.</p>
                    <p class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Sistem Aktif & Terhubung</span>
                    </p>
                </div>
            </footer>
        </div>
    </div>

    @livewireScripts
</body>
</html>

