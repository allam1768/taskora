@props([
    'mobile' => false,
])

@php
    $currentRoute = request()->route()?->getName();
    $currentScope = request()->query('scope') ?? (request()->query('my') ? 'my' : null);

    $isDashboardActive = request()->routeIs('dashboard') && empty($currentScope);
    $isAllTasksActive = (request()->routeIs('tasks') || (request()->routeIs('dashboard') && request()->has('status'))) && $currentScope !== 'my';
    $isMyTasksActive = $currentScope === 'my';
    $isTeamActive = request()->routeIs('team.members');
    $isProfileActive = request()->routeIs('profile');

    $user = auth()->user();
@endphp

<aside
    {{ $attributes->merge(['class' => 'flex flex-col h-full bg-white border-r border-slate-200/80 select-none']) }}
>
    {{-- Brand Logo & App Name --}}
    <div class="h-16 px-6 flex items-center justify-between border-b border-slate-100 flex-shrink-0">
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-indigo-100 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <span class="text-xl font-bold tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">Taskora</span>
                <span class="block text-[10px] font-semibold uppercase tracking-wider text-indigo-600">Task Management</span>
            </div>
        </a>

        @if($mobile)
            <button
                type="button"
                @click="mobileMenuOpen = false"
                class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition"
                aria-label="Tutup Menu"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Tagline pill --}}
    <div class="px-6 pt-4 pb-2">
        <div class="px-3 py-1.5 rounded-lg bg-indigo-50/70 border border-indigo-100/60 flex items-center justify-between">
            <span class="text-[11px] font-medium text-indigo-700 italic">Organize Work. Achieve More.</span>
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
        </div>
    </div>

    {{-- Main Navigation Menu --}}
    <div class="flex-1 overflow-y-auto px-4 py-3 space-y-1">
        <div class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
            Menu Utama
        </div>

        {{-- 1. Dashboard --}}
        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ $isDashboardActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0 {{ $isDashboardActive ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- 2. All Tasks --}}
        <a
            href="{{ route('tasks') }}"
            wire:navigate
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ $isAllTasksActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0 {{ $isAllTasksActive ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span class="flex-1">All Tasks</span>
        </a>

        {{-- 3. My Tasks --}}
        <a
            href="{{ route('tasks', ['scope' => 'my']) }}"
            wire:navigate
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ $isMyTasksActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0 {{ $isMyTasksActive ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="flex-1">My Tasks</span>
            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $isMyTasksActive ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600' }}">Personal</span>
        </a>

        {{-- 4. Team Members --}}
        <a
            href="{{ route('team.members') }}"
            wire:navigate
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ $isTeamActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0 {{ $isTeamActive ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span>Team Members</span>
        </a>

        <div class="pt-4 px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
            Akun & Pengaturan
        </div>

        {{-- 5. Profile --}}
        <a
            href="{{ route('profile') }}"
            wire:navigate
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-150 {{ $isProfileActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
        >
            <svg class="w-5 h-5 flex-shrink-0 {{ $isProfileActive ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>Profile</span>
        </a>
    </div>

    {{-- Footer: User Info & Logout --}}
    <div class="p-4 border-t border-slate-100 bg-slate-50/60">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold uppercase shadow-sm flex-shrink-0">
                    {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-800 truncate">{{ $user?->name ?? 'Guest User' }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-medium capitalize {{ $user?->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-slate-200 text-slate-700' }}">
                            {{ $user?->role ?? 'member' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Logout Form --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100/80 border border-rose-200/80 rounded-xl transition duration-150 cursor-pointer"
            >
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </div>
</aside>

