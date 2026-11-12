<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskora &mdash; Task Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col justify-between">
    {{-- Top Navbar --}}
    <header class="w-full bg-white border-b border-slate-200/80 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-indigo-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Taskora</span>
                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-indigo-600">Task Management</span>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition"
                    >
                        Buka Dashboard &rarr;
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-indigo-600 transition"
                    >
                        Masuk
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition"
                    >
                        Daftar Akun
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Hero Section --}}
    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16">
        <div class="max-w-3xl text-center space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-xs font-semibold text-indigo-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Taskora &bull; Organize Work. Achieve More.</span>
            </div>

            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                Kelola Pekerjaan Tim dengan <span class="text-indigo-600">Lebih Cerdas & Terarah</span>
            </h1>

            <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Pantau progres tugas, status pekerjaan, prioritas, dan deadline tim dalam satu dashboard modern yang intuitif dan terintegrasi.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-md shadow-indigo-200 transition"
                    >
                        Masuk ke Dashboard Saya &rarr;
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-md shadow-indigo-200 transition"
                    >
                        Masuk ke Aplikasi
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-semibold text-sm transition"
                    >
                        Buat Akun Baru
                    </a>
                @endauth
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Taskora &mdash; Task Management System. All rights reserved.
    </footer>
</body>
</html>
