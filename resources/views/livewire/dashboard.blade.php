<div class="space-y-6 sm:space-y-8">
    {{-- 1. Greeting Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-indigo-800 text-white p-6 sm:p-8 shadow-xl shadow-indigo-100/50">
        {{-- Background decorative shapes --}}
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute right-40 -bottom-10 h-48 w-48 rounded-full bg-indigo-400/20 blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold uppercase tracking-wider text-indigo-100 mb-3 border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Taskora Workspace</span>
                    @if($isDbConnected)
                        <span class="text-white/40">&bull;</span>
                        <span class="text-emerald-300">Live Database Connected</span>
                    @else
                        <span class="text-white/40">&bull;</span>
                        <span class="text-indigo-200">Interactive Preview Data</span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Welcome back, {{ $user?->name ?? 'Team Member' }}! 👋
                </h1>
                <p class="mt-2 text-sm sm:text-base text-indigo-100/90 leading-relaxed font-normal">
                    Organize Work. Achieve More. Pantau perkembangan tugas, status pekerjaan tim, dan target deadline Anda hari ini.
                </p>
            </div>

            {{-- Quick summary mini pill --}}
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/15 self-start md:self-auto">
                <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-indigo-200 block font-medium">Hari ini</span>
                    <span class="text-sm font-bold text-white">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Statistics Cards Grid (Section 2.E & 2.F) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        {{-- Card: Total Tasks --}}
        <div
            wire:click="setStatus('all')"
            class="group cursor-pointer rounded-2xl bg-white p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-300 transition-all duration-200 {{ $status === 'all' ? 'ring-2 ring-indigo-500 border-transparent' : '' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Tasks</span>
                <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $statistics['total'] }}</span>
                <span class="text-xs text-slate-400 font-medium">Semua tugas</span>
            </div>
            <div class="mt-3 text-xs text-indigo-600 font-medium flex items-center gap-1 group-hover:underline">
                <span>Klik untuk tampilkan semua</span>
                <span>&rarr;</span>
            </div>
        </div>

        {{-- Card: To Do --}}
        <div
            wire:click="setStatus('todo')"
            class="group cursor-pointer rounded-2xl bg-white p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-300 transition-all duration-200 {{ $status === 'todo' ? 'ring-2 ring-amber-500 border-transparent' : '' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">To Do</span>
                <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $statistics['todo'] }}</span>
                <span class="text-xs text-amber-600 font-medium">Belum dikerjakan</span>
            </div>
            <div class="mt-3 text-xs text-amber-600 font-medium flex items-center gap-1 group-hover:underline">
                <span>Filter tugas To Do</span>
                <span>&rarr;</span>
            </div>
        </div>

        {{-- Card: In Progress --}}
        <div
            wire:click="setStatus('in_progress')"
            class="group cursor-pointer rounded-2xl bg-white p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-sky-300 transition-all duration-200 {{ $status === 'in_progress' ? 'ring-2 ring-sky-500 border-transparent' : '' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-sky-700">In Progress</span>
                <div class="h-10 w-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $statistics['in_progress'] }}</span>
                <span class="text-xs text-sky-600 font-medium">Sedang berjalan</span>
            </div>
            <div class="mt-3 text-xs text-sky-600 font-medium flex items-center gap-1 group-hover:underline">
                <span>Filter tugas In Progress</span>
                <span>&rarr;</span>
            </div>
        </div>

        {{-- Card: Done --}}
        <div
            wire:click="setStatus('done')"
            class="group cursor-pointer rounded-2xl bg-white p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-300 transition-all duration-200 {{ $status === 'done' ? 'ring-2 ring-emerald-500 border-transparent' : '' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Done</span>
                <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $statistics['done'] }}</span>
                <span class="text-xs text-emerald-600 font-medium">Sudah selesai</span>
            </div>
            <div class="mt-3 text-xs text-emerald-600 font-medium flex items-center gap-1 group-hover:underline">
                <span>Filter tugas selesai</span>
                <span>&rarr;</span>
            </div>
        </div>
    </div>

    {{-- 3. Work Progress Bar & Upcoming Deadlines (Section 2.G & 2.D) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Work Progress Card (Spans 2 columns on lg) --}}
        <div class="lg:col-span-2 rounded-2xl bg-white p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Overall Work Progress</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Persentase penyelesaian seluruh pekerjaan tim Taskora.</p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold text-indigo-600 tracking-tight">{{ $statistics['progress'] }}%</span>
                        <span class="text-xs text-slate-400 block font-medium">Terselesaikan</span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="relative w-full h-4 bg-slate-100 rounded-full overflow-hidden p-0.5">
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-indigo-500 via-indigo-600 to-emerald-500 transition-all duration-700 ease-out"
                        style="width: {{ $statistics['progress'] }}%;"
                    ></div>
                </div>

                {{-- Formula & Detail Footnote --}}
                <div class="mt-4 grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 text-center">
                    <div class="p-2 rounded-xl bg-slate-50">
                        <span class="text-xs font-bold text-slate-800">{{ $statistics['done'] }} / {{ $statistics['total'] }}</span>
                        <span class="text-[11px] text-slate-500 block">Selesai vs Total</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50">
                        <span class="text-xs font-bold text-slate-800">{{ $statistics['in_progress'] + $statistics['todo'] }}</span>
                        <span class="text-[11px] text-slate-500 block">Sisa Tugas Aktif</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50">
                        <span class="text-xs font-bold text-emerald-600">{{ $statistics['progress'] }}%</span>
                        <span class="text-[11px] text-slate-500 block">Tingkat Capaian</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Rumus: (Tugas Selesai / Total Tugas) &times; 100%</span>
                @if($statistics['total'] === 0)
                    <span class="text-amber-500 font-medium">Belum ada tugas tercatat</span>
                @endif
            </div>
        </div>

        {{-- Upcoming Deadlines Section --}}
        <div class="rounded-2xl bg-white p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-slate-900">Upcoming Deadlines</h2>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700">Prioritas</span>
                </div>

                <div class="space-y-3">
                    @forelse($upcomingDeadlines as $deadlineTask)
                        @php
                            $dueDate = \Carbon\Carbon::parse($deadlineTask->due_date);
                            $isOverdue = $dueDate->isPast() && !$dueDate->isToday();
                            $isToday = $dueDate->isToday();
                        @endphp
                        <div class="p-3 rounded-xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-xs font-semibold text-slate-800 line-clamp-1">{{ $deadlineTask->title }}</p>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase flex-shrink-0 {{ $isOverdue ? 'bg-rose-100 text-rose-700' : ($isToday ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700') }}">
                                    {{ $isOverdue ? 'Overdue' : ($isToday ? 'Hari Ini' : $dueDate->diffForHumans()) }}
                                </span>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-[11px] text-slate-500">
                                <span>{{ $deadlineTask->assigned_user_name }}</span>
                                <span class="font-medium text-slate-700">{{ $dueDate->format('d M Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs">Tidak ada deadline mendesak.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                <button
                    wire:click="setStatus('todo')"
                    class="w-full text-center text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition"
                >
                    Lihat semua tugas pending &rarr;
                </button>
            </div>
        </div>
    </div>

    {{-- 4. Search & Filter Bar (Section 2.I) --}}
    <div class="rounded-2xl bg-white p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Task Explorer & Filters</h2>
                <p class="text-xs text-slate-500 mt-0.5">Cari tugas berdasarkan judul dan filter menurut status maupun prioritas secara instan.</p>
            </div>

            {{-- My Tasks toggle --}}
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

        {{-- Filters Control Inputs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2">
            {{-- Search Input --}}
            <div class="relative sm:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-600 mb-1">Cari Berdasarkan Judul</label>
                <div class="relative">
                    <input
                        wire:model.live.debounce.300ms="search"
                        id="search"
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

            {{-- Filter Status Dropdown --}}
            <div>
                <label for="statusFilter" class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select
                    wire:model.live="status"
                    id="statusFilter"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-150"
                >
                    <option value="all">Semua Status</option>
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="done">Done</option>
                </select>
            </div>

            {{-- Filter Priority Dropdown --}}
            <div>
                <label for="priorityFilter" class="block text-xs font-semibold text-slate-600 mb-1">Prioritas</label>
                <select
                    wire:model.live="priority"
                    id="priorityFilter"
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

    {{-- 5. Latest Tasks / Recent Tasks (Section 2.H & 2.J) --}}
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        {{-- Table Header Banner --}}
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">Daftar Tugas Terbaru</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $tasks->total() }} tugas
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Daftar tugas terurut yang aktif dan dapat difilter secara real-time.</p>
            </div>

            <div wire:loading class="text-xs text-indigo-600 font-medium flex items-center gap-1.5 self-start sm:self-auto">
                <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Memperbarui data...</span>
            </div>
        </div>

        {{-- Desktop & Tablet Table View (hidden on very small screens) --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-3.5">Nama Tugas</th>
                        <th class="px-6 py-3.5">Anggota Ditugaskan</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Prioritas</th>
                        <th class="px-6 py-3.5">Deadline</th>
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
                            {{-- Task Title & Description --}}
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 line-clamp-1">{{ $task->title }}</div>
                                @if(!empty($task->description))
                                    <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $task->description }}</div>
                                @endif
                            </td>

                            {{-- Assigned Member --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-full bg-gradient-to-tr from-slate-200 to-indigo-100 border border-slate-200 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($task->assigned_user_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-slate-800">{{ $task->assigned_user_name ?? 'Belum Ditugaskan' }}</span>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($status === 'done' || $status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Done</span>
                                    </span>
                                @elseif($status === 'in_progress' || $status === 'in progress')
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

                            {{-- Priority Badge --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($priority === 'high')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        High
                                    </span>
                                @elseif($priority === 'low')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        Low
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Medium
                                    </span>
                                @endif
                            </td>

                            {{-- Deadline --}}
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
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

        {{-- Mobile Card List View (Visible on small screens) --}}
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
                        {{-- Priority badge --}}
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
                        {{-- Assignee --}}
                        <div class="flex items-center gap-1.5 text-slate-700 font-medium">
                            <div class="h-6 w-6 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-bold flex items-center justify-center">
                                {{ strtoupper(substr($task->assigned_user_name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="truncate max-w-[120px]">{{ $task->assigned_user_name ?? 'Unassigned' }}</span>
                        </div>

                        {{-- Status Badge --}}
                        @if($status === 'done' || $status === 'completed')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Done</span>
                        @elseif($status === 'in_progress' || $status === 'in progress')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">In Progress</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">To Do</span>
                        @endif
                    </div>

                    {{-- Deadline Footer --}}
                    @if($dueDate)
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500 pt-1 border-t border-slate-100">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Deadline: <strong class="{{ $dueDate->isPast() && !$dueDate->isToday() && $status !== 'done' ? 'text-rose-600' : 'text-slate-700' }}">{{ $dueDate->format('d M Y') }}</strong></span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-xs font-semibold text-slate-700">No tasks available</p>
                    <p class="text-[11px] text-slate-400 mt-1">Tidak ada tugas yang sesuai.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Footer --}}
        @if($tasks->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>

