@props([
    'title' => 'Dashboard',
    'subtitle' => null,
])

@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-30 h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between transition-all">
    {{-- Left: Mobile Toggle & Page Title --}}
    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        {{-- Mobile Hamburger Button --}}
        <button
            type="button"
            @click="mobileMenuOpen = true"
            class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            aria-label="Buka Menu Sidebar"
        >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="min-w-0">
            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 truncate">
                {{ $title }}
            </h1>
            @if($subtitle)
                <p class="text-xs text-slate-500 hidden sm:block truncate">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    {{-- Center/Right: Search bar, User Info, Actions --}}
    <div class="flex items-center gap-2 sm:gap-4">
        {{-- Navbar Search Input --}}
        <div class="relative hidden md:block w-48 lg:w-72">
            <form action="{{ route('tasks') }}" method="GET">
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        placeholder="Search tasks..."
                        value="{{ request()->query('q') }}"
                        class="w-full bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-150"
                    />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>

        {{-- Divider --}}
        <div class="hidden sm:block h-6 w-px bg-slate-200"></div>

        {{-- Logged-in User Profile Dropdown / Card --}}
        <div class="flex items-center gap-2 sm:gap-3" x-data="{ userMenuOpen: false }">
            <a
                href="{{ route('profile') }}"
                wire:navigate
                class="flex items-center gap-2.5 p-1 sm:p-1.5 rounded-xl hover:bg-slate-100/80 transition duration-150 group"
                title="Buka Pengaturan Profil"
            >
                <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white text-xs sm:text-sm font-bold uppercase shadow-sm group-hover:scale-105 transition-transform duration-150">
                    {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                </div>
                <div class="hidden sm:block text-left">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold text-slate-800 leading-none group-hover:text-indigo-600 transition">{{ $user?->name }}</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider {{ $user?->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $user?->role ?? 'member' }}
                        </span>
                    </div>
                    <span class="text-[11px] text-slate-400 leading-none block mt-1 truncate max-w-[150px]">{{ $user?->email }}</span>
                </div>
            </a>

            {{-- Quick Logout button in navbar --}}
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button
                    type="submit"
                    title="Keluar dari akun"
                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition duration-150 cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>

