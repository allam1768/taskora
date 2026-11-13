<div class="space-y-6 sm:space-y-8">
    @php
        $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done'];
        $priorityLabels = ['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
    @endphp

    {{-- Flash Notification --}}
    @if (session()->has('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200/80 p-4 flex items-center gap-3 text-emerald-800 shadow-xs">
            <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-medium">{{ session('status') }}</p>
        </div>
    @endif

    {{-- 1. Page Header & Stats Summary --}}
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-slate-900">Task Management</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                    {{ $tasks->total() }} tugas
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Kelola seluruh tugas tim: tambah, ubah status, atur prioritas, dan pantau deadline.</p>

            <div class="flex flex-wrap items-center gap-1.5 mt-3">
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold">Total {{ $stats['total'] }}</span>
                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold">To Do {{ $stats['todo'] }}</span>
                <span class="px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 text-[11px] font-semibold">In Progress {{ $stats['in_progress'] }}</span>
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">Done {{ $stats['done'] }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3 self-start lg:self-auto">
            @can('create', \App\Models\Task::class)
                <button
                    type="button"
                    wire:click="openCreate"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm shadow-indigo-200 transition duration-150"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Tugas</span>
                </button>
            @endcan
        </div>
    </div>

    {{-- 2. Search & Filter Bar --}}
    <div class="rounded-2xl bg-white p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Cari & Filter Tugas</h2>
                <p class="text-xs text-slate-500 mt-0.5">Filter berdasarkan judul, status, prioritas, maupun lingkup tugas Anda.</p>
            </div>

            <div class="flex items-center gap-2 self-start lg:self-auto">
                <button
                    type="button"
                    wire:click="toggleMyTasks"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold border transition duration-150 {{ $onlyMy ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ $onlyMy ? 'Menampilkan: Tugas Saya' : 'Tampilkan: Semua Tugas' }}</span>
                </button>

                @if($search !== '' || $status !== 'all' || $priority !== 'all' || $onlyMy)
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100/80 border border-rose-200/80 transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Reset Filter</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2">
            <div class="relative sm:col-span-2">
                <label for="taskSearch" class="block text-xs font-semibold text-slate-600 mb-1">Cari Berdasarkan Judul</label>
                <div class="relative">
                    <input
                        wire:model.live.debounce.300ms="search"
                        id="taskSearch"
                        type="text"
                        placeholder="Ketik judul atau deskripsi tugas..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-9 pr-8 py-2 text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-150"
                    />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 sm:top-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if(!empty($search))
                        <button
                            wire:click="$set('search', '')"
                            class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600"
                            title="Hapus pencarian"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <div>
                <label for="taskStatusFilter" class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select
                    wire:model.live="status"
                    id="taskStatusFilter"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-150"
                >
                    <option value="all">Semua Status</option>
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="done">Done</option>
                </select>
            </div>

            <div>
                <label for="taskPriorityFilter" class="block text-xs font-semibold text-slate-600 mb-1">Prioritas</label>
                <select
                    wire:model.live="priority"
                    id="taskPriorityFilter"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-150"
                >
                    <option value="all">Semua Prioritas</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>
    </div>

    {{-- 3. Task List Table --}}
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">Daftar Tugas</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $tasks->total() }} tugas
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Daftar tugas terbaru, terfilter secara real-time, dan siap dikelola.</p>
            </div>

            <div wire:loading class="text-xs text-indigo-600 font-medium flex items-center gap-1.5 self-start sm:self-auto">
                <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Memperbarui data...</span>
            </div>
        </div>

        {{-- Desktop & Tablet Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-3.5">Nama Tugas</th>
                        <th class="px-6 py-3.5">Anggota Ditugaskan</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Prioritas</th>
                        <th class="px-6 py-3.5">Deadline</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tasks as $task)
                        @php
                            $status = strtolower($task->status);
                            $priority = strtolower($task->priority);
                            $dueDate = !empty($task->due_date) ? \Carbon\Carbon::parse($task->due_date) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 line-clamp-1">{{ $task->title }}</div>
                                @if(!empty($task->description))
                                    <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $task->description }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-full bg-gradient-to-tr from-slate-200 to-indigo-100 border border-slate-200 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($task->assignee?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-slate-800">{{ $task->assignee?->name ?? 'Belum Ditugaskan' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($status === 'done')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Done</span>
                                    </span>
                                @elseif($status === 'in_progress')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                        <span>In Progress</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>To Do</span>
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($priority === 'high')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">High</span>
                                @elseif($priority === 'low')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">Low</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Medium</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($dueDate)
                                    <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="{{ $dueDate->isPast() && !$dueDate->isToday() && $status !== 'done' ? 'text-rose-600 font-semibold' : '' }}">
                                            {{ $dueDate->format('d M Y') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('update', $task)
                                        <button
                                            wire:click="cycleStatus({{ $task->id }})"
                                            title="Putar status tugas"
                                            class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition duration-150"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="openEdit({{ $task->id }})"
                                            title="Edit tugas"
                                            class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition duration-150"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                    @endcan

                                    @can('delete', $task)
                                        <button
                                            wire:click="confirmDelete({{ $task->id }})"
                                            title="Hapus tugas"
                                            class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition duration-150"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="max-w-xs mx-auto">
                                    <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-slate-800">No tasks available</h3>
                                    <p class="mt-1 text-xs text-slate-500">Tidak ada tugas yang sesuai dengan kriteria filter atau pencarian Anda.</p>
                                    @if($search !== '' || $status !== 'all' || $priority !== 'all' || $onlyMy)
                                        <button
                                            wire:click="resetFilters"
                                            class="mt-3 text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                                        >
                                            Reset Filter
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List View --}}
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse($tasks as $task)
                @php
                    $status = strtolower($task->status);
                    $priority = strtolower($task->priority);
                    $dueDate = !empty($task->due_date) ? \Carbon\Carbon::parse($task->due_date) : null;
                @endphp
                <div class="p-4 space-y-3 hover:bg-slate-50/60 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="font-semibold text-sm text-slate-900 leading-snug">{{ $task->title }}</div>
                        @if($priority === 'high')
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">High</span>
                        @elseif($priority === 'low')
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Low</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Medium</span>
                        @endif
                    </div>

                    @if(!empty($task->description))
                        <p class="text-xs text-slate-500 line-clamp-2">{{ $task->description }}</p>
                    @endif

                    <div class="flex items-center justify-between text-xs pt-1">
                        <div class="flex items-center gap-1.5 text-slate-700 font-medium">
                            <div class="h-6 w-6 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-bold flex items-center justify-center">
                                {{ strtoupper(substr($task->assignee?->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="truncate max-w-[120px]">{{ $task->assignee?->name ?? 'Belum Ditugaskan' }}</span>
                        </div>

                        @if($status === 'done')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Done</span>
                        @elseif($status === 'in_progress')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">In Progress</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">To Do</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-1.5 text-[11px] text-slate-500 pt-1 border-t border-slate-100">
                        <span class="flex items-center gap-1.5">
                            @if($dueDate)
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Deadline: <strong class="{{ $dueDate->isPast() && !$dueDate->isToday() && $status !== 'done' ? 'text-rose-600' : 'text-slate-700' }}">{{ $dueDate->format('d M Y') }}</strong></span>
                            @else
                                <span class="text-slate-400">Tanpa deadline</span>
                            @endif
                        </span>

                            <div class="flex items-center gap-1">
                                @can('update', $task)
                                    <button
                                        wire:click="cycleStatus({{ $task->id }})"
                                        title="Putar status tugas"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="openEdit({{ $task->id }})"
                                        title="Edit tugas"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                @endcan
                                @can('delete', $task)
                                    <button
                                        wire:click="confirmDelete({{ $task->id }})"
                                        title="Hapus tugas"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-xs font-semibold text-slate-700">No tasks available</p>
                    <p class="text-[11px] text-slate-400 mt-1">Tidak ada tugas yang sesuai.</p>
                </div>
            @endforelse
        </div>

        @if($tasks->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>

    {{-- 4. Create / Edit Task Modal --}}
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
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ $editingId ? 'Edit Tugas' : 'Tambah Tugas Baru' }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $editingId ? 'Perbarui detail tugas yang sudah ada.' : 'Isi detail tugas untuk ditugaskan ke anggota tim.' }}
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="closeForm"
                        class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition"
                        title="Tutup"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4 overflow-y-auto">
                    {{-- Title --}}
                    <div>
                        <label for="taskTitle" class="block text-xs font-semibold text-slate-600 mb-1">Judul Tugas</label>
                        <input
                            wire:model="title"
                            id="taskTitle"
                            type="text"
                            placeholder="Contoh: Bangun halaman dashboard"
                            class="w-full rounded-xl border @error('title') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/50 @enderror bg-slate-50/50 focus:bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition duration-150"
                        />
                        @error('title')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="taskDescription" class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea
                            wire:model="description"
                            id="taskDescription"
                            rows="3"
                            placeholder="Jelaskan detail pekerjaan, hasil yang diharapkan, dan referensi yang relevan..."
                            class="w-full rounded-xl border @error('description') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/50 @enderror bg-slate-50/50 focus:bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 transition duration-150 resize-none"
                        ></textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Status --}}
                        <div>
                            <label for="taskStatus" class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                            <select
                                wire:model="formStatus"
                                id="taskStatus"
                                class="w-full rounded-xl border @error('formStatus') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/50 @enderror bg-slate-50/50 focus:bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 transition duration-150"
                            >
                                <option value="todo">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                            @error('formStatus')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div>
                            <label for="taskPriority" class="block text-xs font-semibold text-slate-600 mb-1">Prioritas</label>
                            <select
                                wire:model="formPriority"
                                id="taskPriority"
                                class="w-full rounded-xl border @error('formPriority') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/50 @enderror bg-slate-50/50 focus:bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 transition duration-150"
                            >
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                            @error('formPriority')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Due Date --}}
                        <div>
                            <label for="taskDueDate" class="block text-xs font-semibold text-slate-600 mb-1">Deadline</label>
                            <input
                                wire:model="dueDate"
                                id="taskDueDate"
                                type="date"
                                class="w-full rounded-xl border @error('dueDate') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/50 @enderror bg-slate-50/50 focus:bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 transition duration-150"
                            />
                            @error('dueDate')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Assignee --}}
                        <div>
                            <label for="taskAssignee" class="block text-xs font-semibold text-slate-600 mb-1">Ditugaskan Ke</label>
                            <select
                                wire:model="assignee"
                                id="taskAssignee"
                                class="w-full rounded-xl border @error('assignee') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/50 @enderror bg-slate-50/50 focus:bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 transition duration-150"
                            >
                                <option value="">Belum Ditugaskan</option>
                                @foreach($users as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} &mdash; {{ $member->email }}</option>
                                @endforeach
                            </select>
                            @error('assignee')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex justify-end gap-3 flex-shrink-0">
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
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Buat Tugas' }}</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 5. Delete Confirmation Modal --}}
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
            class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200/80 p-6 text-center"
        >
            <div class="mx-auto h-12 w-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>

            <h3 class="mt-4 text-base font-bold text-slate-900">Hapus Tugas Ini?</h3>
            <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                Tindakan ini tidak dapat dibatalkan. Tugas
                <span class="font-semibold text-slate-800">{{ $deletingTask?->title }}</span>
                akan dihapus secara permanen.
            </p>

            <div class="mt-6 flex items-center justify-center gap-3">
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
                    <span wire:loading.remove wire:target="deleteTask">Ya, Hapus Tugas</span>
                    <span wire:loading wire:target="deleteTask">Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
</div>
