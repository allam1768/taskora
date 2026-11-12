<div class="min-h-full bg-slate-50">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                    <div class="h-9 w-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        T
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 group-hover:text-indigo-600 transition">Taskora</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-sm font-medium text-slate-600">Anggota Tim</span>
            </div>

            <div class="flex items-center gap-4">
                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg transition"
                >
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Daftar Anggota Tim</h1>
                <p class="mt-1 text-sm text-slate-500">Seluruh anggota terdaftar yang memiliki akses ke ruang kerja Taskora.</p>
            </div>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6 flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:max-w-xs">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Cari nama atau email..."
                    class="w-full rounded-xl border border-slate-300 pl-9 pr-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label for="roleFilter" class="text-xs font-medium text-slate-600 whitespace-nowrap">Filter Role:</label>
                <select
                    id="roleFilter"
                    wire:model.live="role"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                >
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="member">Member</option>
                </select>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/75">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                Anggota
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                Role
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                Bergabung Sejak
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($members as $member)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-indigo-50 border border-indigo-200 flex items-center justify-center text-xs font-semibold text-indigo-700">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900">{{ $member->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-600">
                                    {{ $member->email }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if ($member->role === 'admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Member
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                    {{ $member->created_at?->format('d M Y') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Tidak ada anggota tim yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($members->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
