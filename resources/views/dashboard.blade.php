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
                    <span class="text-sm font-medium text-slate-700">{{ auth()->user()?->name }}</span>
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
