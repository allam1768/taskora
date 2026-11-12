<x-layouts.app title="Dashboard - Taskora">
    <div class="min-h-full">
        <header class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        T
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Taskora</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-indigo-50 border border-indigo-200 flex items-center justify-center text-xs font-semibold text-indigo-700">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-slate-800 leading-none">{{ auth()->user()?->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5 leading-none">{{ auth()->user()?->email }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 rounded-lg transition duration-150"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="rounded-2xl bg-white p-8 border border-slate-200 shadow-sm">
                <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
                <p class="mt-2 text-sm text-slate-600">Selamat datang di Task Management System Taskora.</p>
            </div>
        </main>
    </div>
</x-layouts.app>
