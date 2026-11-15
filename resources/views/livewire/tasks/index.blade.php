<div class="space-y-5">
    @php
        $statusTabs = [
            ['key' => 'all', 'label' => 'Semua', 'count' => $stats['total']],
            ['key' => 'todo', 'label' => 'To Do', 'count' => $stats['todo']],
            ['key' => 'in_progress', 'label' => 'In Progress', 'count' => $stats['in_progress']],
            ['key' => 'done', 'label' => 'Done', 'count' => $stats['done']],
        ];

        $hasFilters = $search !== '' || $status !== 'all' || $priority !== 'all' || $onlyMy;
    @endphp

    {{-- Notifikasi --}}
    @if (session()->has('status'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200/80 px-4 py-3 flex items-center gap-2.5 text-emerald-800">
            <svg class="h-4 w-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-medium">{{ session('status') }}</p>
        </div>
    @endif

    {{-- Toolbar: aksi utama, pencarian, filter, dan tab status --}}
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center gap-2.5">
            <div class="relative flex-1 min-w-0">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    id="taskSearch"
                    type="search"
                    placeholder="Cari tugas..."
                    aria-label="Cari tugas"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/60 pl-9 pr-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-150"
                />
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <select
                    wire:model.live="priority"
                    id="taskPriorityFilter"
                    aria-label="Filter prioritas"
                    class="flex-1 sm:flex-none rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-150"
                >
                    <option value="all">Semua prioritas</option>
                    <option value="high">Prioritas tinggi</option>
                    <option value="medium">Prioritas sedang</option>
                    <option value="low">Prioritas rendah</option>
                </select>

                <button
                    type="button"
                    wire:click="toggleMyTasks"
                    title="{{ $onlyMy ? 'Tampilkan semua tugas tim' : 'Tampilkan hanya tugas saya' }}"
                    aria-pressed="{{ $onlyMy ? 'true' : 'false' }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium border transition duration-150 whitespace-nowrap {{ $onlyMy ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="hidden md:inline">Tugas saya</span>
                </button>

                @if($hasFilters)
                    <button
                        type="button"
                        wire:click="resetFilters"
                        title="Reset semua filter"
                        class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200/80 transition duration-150"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="sr-only">Reset filter</span>
                    </button>
                @endif

                @can('create', \App\Models\Task::class)
                    <button
                        type="button"
                        wire:click="openCreate"
                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm shadow-indigo-200 transition duration-150 whitespace-nowrap ml-auto sm:ml-0"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah<span class="hidden sm:inline"> tugas</span></span>
                    </button>
                @endcan
            </div>
        </div>

        {{-- Tab status dengan jumlah tugas --}}
        <div class="px-3 sm:px-4 py-2.5 border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto">
            @foreach($statusTabs as $tab)
                <button
                    type="button"
                    wire:click="setStatus('{{ $tab['key'] }}')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition duration-150 {{ $status === $tab['key'] ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-200' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 rounded-md text-[11px] {{ $status === $tab['key'] ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $tab['count'] }}</span>
                </button>
            @endforeach

            <div wire:loading class="ml-auto pl-3 text-xs text-indigo-600 font-medium flex items-center gap-1.5 flex-shrink-0">
                <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Memuat...</span>
            </div>
        </div>
    </div>

    {{-- Daftar tugas --}}
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3">Tugas</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Prioritas</th>
                        <th class="px-5 py-3">Deadline</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tasks as $task)
                        @php
                            $taskStatus = strtolower($task->status);
                            $taskPriority = strtolower($task->priority);
                            $dueDate = $task->due_date ? \Carbon\Carbon::parse($task->due_date) : null;
                            $isOverdue = $dueDate && $dueDate->isPast() && ! $dueDate->isToday() && $taskStatus !== 'done';
                        @endphp
                        <tr class="group hover:bg-slate-50/70 transition duration-150">
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-slate-900 line-clamp-1">{{ $task->title }}</div>
                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="h-5 w-5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-bold flex items-center justify-center">
                                            {{ strtoupper(substr($task->assignee?->name ?? 'U', 0, 1)) }}
                                        </span>
                                        {{ $task->assignee?->name ?? 'Belum ditugaskan' }}
                                    </span>
                                    @if($task->description)
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="line-clamp-1 hidden lg:inline">{{ $task->description }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @can('update', $task)
                                    <button
                                        type="button"
                                        wire:click="cycleStatus({{ $task->id }})"
                                        title="Klik untuk mengubah status"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition duration-150 hover:ring-2 hover:ring-offset-1
                                            {{ $taskStatus === 'done' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/80 hover:ring-emerald-200' : ($taskStatus === 'in_progress' ? 'bg-sky-50 text-sky-700 border-sky-200/80 hover:ring-sky-200' : 'bg-amber-50 text-amber-700 border-amber-200/80 hover:ring-amber-200') }}"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full {{ $taskStatus === 'done' ? 'bg-emerald-500' : ($taskStatus === 'in_progress' ? 'bg-sky-500 animate-pulse' : 'bg-amber-500') }}"></span>
                                        <span>{{ $taskStatus === 'done' ? 'Done' : ($taskStatus === 'in_progress' ? 'In Progress' : 'To Do') }}</span>
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $taskStatus === 'done' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/80' : ($taskStatus === 'in_progress' ? 'bg-sky-50 text-sky-700 border-sky-200/80' : 'bg-amber-50 text-amber-700 border-amber-200/80') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $taskStatus === 'done' ? 'bg-emerald-500' : ($taskStatus === 'in_progress' ? 'bg-sky-500' : 'bg-amber-500') }}"></span>
                                        <span>{{ $taskStatus === 'done' ? 'Done' : ($taskStatus === 'in_progress' ? 'In Progress' : 'To Do') }}</span>
                                    </span>
                                @endcan
                            </td>

                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $taskPriority === 'high' ? 'bg-rose-500' : ($taskPriority === 'low' ? 'bg-slate-400' : 'bg-amber-500') }}"></span>
                                    {{ $taskPriority === 'high' ? 'Tinggi' : ($taskPriority === 'low' ? 'Rendah' : 'Sedang') }}
                                </span>
                            </td>

                            <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                                @if($dueDate)
                                    <span class="{{ $isOverdue ? 'text-rose-600 font-semibold' : 'text-slate-600' }}">
                                        {{ $dueDate->format('d M Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <span class="block text-[11px] text-rose-500 font-medium">Lewat deadline</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">&mdash;</span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @can('update', $task)
                                        <button
                                            type="button"
                                            wire:click="openEdit({{ $task->id }})"
                                            title="Edit tugas"
                                            class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition duration-150"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            <span class="sr-only">Edit</span>
                                        </button>
                                    @endcan

                                    @can('delete', $task)
                                        <button
                                            type="button"
                                            wire:click="confirmDelete({{ $task->id }})"
                                            title="Hapus tugas"
                                            class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition duration-150"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span class="sr-only">Hapus</span>
                                        </button>
                                    @endcan

                                    @if(!auth()->user()->can('update', $task) && !auth()->user()->can('delete', $task))
                                        <span class="text-xs text-slate-300 px-2">&mdash;</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-14 text-center">
                                <div class="max-w-sm mx-auto">
                                    <div class="mx-auto h-11 w-11 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-slate-800">
                                        {{ $hasFilters ? 'Tidak ada tugas yang cocok' : 'Belum ada tugas' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $hasFilters ? 'Coba ubah kata kunci atau reset filter untuk melihat tugas lain.' : 'Mulai dengan menambahkan tugas pertama untuk tim Anda.' }}
                                    </p>
                                    <div class="mt-4 flex items-center justify-center gap-2">
                                        @if($hasFilters)
                                            <button type="button" wire:click="resetFilters" class="px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition">
                                                Reset filter
                                            </button>
                                        @endif
                                        @can('create', \App\Models\Task::class)
                                            <button type="button" wire:click="openCreate" class="px-3 py-2 rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                                Tambah tugas
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tampilan kartu untuk layar kecil --}}
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse($tasks as $task)
                @php
                    $taskStatus = strtolower($task->status);
                    $taskPriority = strtolower($task->priority);
                    $dueDate = $task->due_date ? \Carbon\Carbon::parse($task->due_date) : null;
                    $isOverdue = $dueDate && $dueDate->isPast() && ! $dueDate->isToday() && $taskStatus !== 'done';
                @endphp
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-900 leading-snug">{{ $task->title }}</p>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-slate-500 flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full {{ $taskPriority === 'high' ? 'bg-rose-500' : ($taskPriority === 'low' ? 'bg-slate-400' : 'bg-amber-500') }}"></span>
                            {{ $taskPriority === 'high' ? 'Tinggi' : ($taskPriority === 'low' ? 'Rendah' : 'Sedang') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1.5 min-w-0">
                            <span class="h-5 w-5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-bold flex items-center justify-center flex-shrink-0">
                                {{ strtoupper(substr($task->assignee?->name ?? 'U', 0, 1)) }}
                            </span>
                            <span class="truncate">{{ $task->assignee?->name ?? 'Belum ditugaskan' }}</span>
                        </span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="{{ $isOverdue ? 'text-rose-600 font-semibold' : '' }}">
                            {{ $dueDate ? $dueDate->format('d M Y') : 'Tanpa deadline' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-0.5">
                        @if($taskStatus === 'done')
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">Done</span>
                        @elseif($taskStatus === 'in_progress')
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200/80">In Progress</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">To Do</span>
                        @endif

                        <div class="flex items-center gap-1">
                            @can('update', $task)
                                <button wire:click="cycleStatus({{ $task->id }})" title="Ubah status" class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                                <button wire:click="openEdit({{ $task->id }})" title="Edit tugas" class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endcan
                            @can('delete', $task)
                                <button wire:click="confirmDelete({{ $task->id }})" title="Hapus tugas" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-sm font-semibold text-slate-800">{{ $hasFilters ? 'Tidak ada tugas yang cocok' : 'Belum ada tugas' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $hasFilters ? 'Coba reset filter untuk melihat semua tugas.' : 'Tambahkan tugas pertama untuk tim Anda.' }}</p>
                    <div class="mt-4 flex items-center justify-center gap-2">
                        @if($hasFilters)
                            <button type="button" wire:click="resetFilters" class="px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-200">Reset filter</button>
                        @endif
                        @can('create', \App\Models\Task::class)
                            <button type="button" wire:click="openCreate" class="px-3 py-2 rounded-lg text-xs font-semibold text-white bg-indigo-600">Tambah tugas</button>
                        @endcan
                    </div>
                </div>
            @endforelse
        </div>

        @if($tasks->hasPages())
            <div class="p-3 sm:p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>

    {{-- Modal tambah / edit tugas --}}
    <div
        x-data
        x-show="$wire.showForm"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        @keydown.escape.window="$wire.closeForm()"
    >
        <div
            x-show="$wire.showForm"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs"
            @click="$wire.closeForm()"
        ></div>

        <div
            x-show="$wire.showForm"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
            class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden"
        >
            <form wire:submit="save" class="flex flex-col max-h-[90vh]">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                    <h2 class="text-base font-bold text-slate-900">{{ $editingId ? 'Edit tugas' : 'Tambah tugas' }}</h2>
                    <button
                        type="button"
                        wire:click="closeForm"
                        class="p-2 -mr-1 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <div class="px-5 sm:px-6 py-5 space-y-4 overflow-y-auto">
                    <div>
                        <label for="taskTitle" class="block text-xs font-semibold text-slate-600 mb-1.5">Judul tugas</label>
                        <input
                            wire:model="title"
                            id="taskTitle"
                            type="text"
                            placeholder="Contoh: Bangun halaman dashboard"
                            class="w-full rounded-xl border @error('title') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/40 @enderror bg-slate-50/60 focus:bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition duration-150"
                        />
                        @error('title')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="taskDescription" class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Deskripsi <span class="font-normal text-slate-400">(opsional)</span>
                        </label>
                        <textarea
                            wire:model="description"
                            id="taskDescription"
                            rows="3"
                            placeholder="Detail pekerjaan, hasil yang diharapkan, atau referensi..."
                            class="w-full rounded-xl border @error('description') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/40 @enderror bg-slate-50/60 focus:bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition duration-150 resize-none"
                        ></textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="taskStatus" class="block text-xs font-semibold text-slate-600 mb-1.5">Status</label>
                            <select
                                wire:model="formStatus"
                                id="taskStatus"
                                class="w-full rounded-xl border @error('formStatus') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/40 @enderror bg-slate-50/60 focus:bg-white px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 transition duration-150"
                            >
                                <option value="todo">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                            @error('formStatus')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="taskPriority" class="block text-xs font-semibold text-slate-600 mb-1.5">Prioritas</label>
                            <select
                                wire:model="formPriority"
                                id="taskPriority"
                                class="w-full rounded-xl border @error('formPriority') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/40 @enderror bg-slate-50/60 focus:bg-white px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 transition duration-150"
                            >
                                <option value="high">Tinggi</option>
                                <option value="medium">Sedang</option>
                                <option value="low">Rendah</option>
                            </select>
                            @error('formPriority')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="taskDueDate" class="block text-xs font-semibold text-slate-600 mb-1.5">Deadline</label>
                            <input
                                wire:model="dueDate"
                                id="taskDueDate"
                                type="date"
                                class="w-full rounded-xl border @error('dueDate') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/40 @enderror bg-slate-50/60 focus:bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 transition duration-150"
                            />
                            @error('dueDate')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="taskAssignee" class="block text-xs font-semibold text-slate-600 mb-1.5">Ditugaskan ke</label>
                            <select
                                wire:model="assignee"
                                id="taskAssignee"
                                class="w-full rounded-xl border @error('assignee') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/40 @enderror bg-slate-50/60 focus:bg-white px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 transition duration-150"
                            >
                                <option value="">Belum ditugaskan</option>
                                @foreach($users as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            @error('assignee')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-5 sm:px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex justify-end gap-2.5 flex-shrink-0">
                    <button
                        type="button"
                        wire:click="closeForm"
                        class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition duration-150"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-sm shadow-indigo-200 transition duration-150 disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan perubahan' : 'Buat tugas' }}</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Konfirmasi hapus --}}
    <div
        x-data
        x-show="$wire.deletingId"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        @keydown.escape.window="$wire.cancelDelete()"
    >
        <div
            x-show="$wire.deletingId"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs"
            @click="$wire.cancelDelete()"
        ></div>

        <div
            x-show="$wire.deletingId"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
            class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200/80 p-6 text-center"
        >
            <div class="mx-auto h-11 w-11 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>

            <h2 class="mt-3 text-base font-bold text-slate-900">Hapus tugas ini?</h2>
            <p class="mt-1.5 text-sm text-slate-500 leading-relaxed">
                <span class="font-semibold text-slate-800">{{ $deletingTask?->title }}</span>
                akan dihapus permanen dan tidak bisa dikembalikan.
            </p>

            <div class="mt-6 flex items-center justify-center gap-2.5">
                <button
                    type="button"
                    wire:click="cancelDelete"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition duration-150"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="deleteTask"
                    wire:loading.attr="disabled"
                    wire:target="deleteTask"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-sm transition duration-150 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="deleteTask">Hapus tugas</span>
                    <span wire:loading wire:target="deleteTask">Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
</div>
