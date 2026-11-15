<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Task Management - Taskora')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $priority = 'all';

    #[Url(as: 'my')]
    public bool $onlyMy = false;

    public int $perPage = 10;

    public bool $showForm = false;

    public ?int $editingId = null;

    public ?int $deletingId = null;

    public string $title = '';

    public string $description = '';

    public string $formStatus = 'todo';

    public string $formPriority = 'medium';

    public ?string $dueDate = null;

    public ?string $assignee = null;

    public function mount(): void
    {
        if (request()->query('scope') === 'my' || request()->query('my') === '1') {
            $this->onlyMy = true;
        }
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'formStatus' => ['required', 'string', Rule::in(['todo', 'in_progress', 'done'])],
            'formPriority' => ['required', 'string', Rule::in(['high', 'medium', 'low'])],
            'dueDate' => ['nullable', 'date'],
            'assignee' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Judul tugas wajib diisi.',
            'title.max' => 'Judul tugas maksimal 255 karakter.',
            'description.max' => 'Deskripsi tugas maksimal 5000 karakter.',
            'formStatus.required' => 'Status tugas wajib dipilih.',
            'formStatus.in' => 'Status tugas tidak valid.',
            'formPriority.required' => 'Prioritas tugas wajib dipilih.',
            'formPriority.in' => 'Prioritas tugas tidak valid.',
            'dueDate.date' => 'Format tanggal deadline tidak valid.',
            'assignee.integer' => 'Anggota yang ditugaskan tidak valid.',
            'assignee.exists' => 'Anggota yang dipilih tidak ditemukan.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPriority(): void
    {
        $this->resetPage();
    }

    public function updatingOnlyMy(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
        $this->resetPage();
    }

    public function toggleMyTasks(): void
    {
        $this->onlyMy = ! $this->onlyMy;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'priority', 'onlyMy']);
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize('create', Task::class);

        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function openEdit(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        Gate::authorize('update', $task);

        $this->editingId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->formStatus = $task->status;
        $this->formPriority = $task->priority;
        $this->dueDate = $task->due_date?->format('Y-m-d');
        $this->assignee = $task->user_id !== null ? (string) $task->user_id : null;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->dueDate = $this->dueDate === '' ? null : $this->dueDate;
        $this->assignee = $this->assignee === '' ? null : $this->assignee;

        $this->validate();

        $data = [
            'title' => trim($this->title),
            'description' => trim($this->description) !== '' ? trim($this->description) : null,
            'status' => $this->formStatus,
            'priority' => $this->formPriority,
            'due_date' => $this->dueDate ?: null,
            'user_id' => $this->assignee !== null ? (int) $this->assignee : null,
        ];

        if ($this->editingId !== null) {
            $task = Task::findOrFail($this->editingId);
            Gate::authorize('update', $task);
            $task->update($data);

            session()->flash('status', "Tugas \"{$task->title}\" berhasil diperbarui.");
        } else {
            Gate::authorize('create', Task::class);
            $task = Task::create($data + ['created_by' => Auth::id()]);

            session()->flash('status', "Tugas \"{$task->title}\" berhasil dibuat.");
        }

        $this->closeForm();
    }

    public function cycleStatus(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        Gate::authorize('update', $task);

        $flow = [
            'todo' => 'in_progress',
            'in_progress' => 'done',
            'done' => 'todo',
        ];

        $next = $flow[$task->status] ?? 'todo';
        $task->update(['status' => $next]);

        session()->flash('status', "Status \"{$task->title}\" diperbarui menjadi {$this->statusLabel($next)}.");
    }

    public function confirmDelete(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        Gate::authorize('delete', $task);

        $this->deletingId = $taskId;
    }

    public function deleteTask(): void
    {
        $task = Task::find($this->deletingId);

        if (! $task) {
            $this->deletingId = null;

            return;
        }

        Gate::authorize('delete', $task);

        $title = $task->title;
        $task->delete();

        $this->deletingId = null;
        $this->resetPage();

        session()->flash('status', "Tugas \"{$title}\" berhasil dihapus.");
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function render()
    {
        $base = Task::query();

        if (trim($this->search) !== '') {
            $keyword = '%'.trim($this->search).'%';
            $base->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword);
            });
        }

        if (in_array($this->priority, ['high', 'medium', 'low'], true)) {
            $base->where('priority', $this->priority);
        }

        if ($this->onlyMy && Auth::check()) {
            $base->where('user_id', Auth::id());
        }

        $stats = [
            'total' => (clone $base)->count(),
            'todo' => (clone $base)->where('status', 'todo')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'done' => (clone $base)->where('status', 'done')->count(),
        ];

        $query = (clone $base)->with('assignee');

        if (in_array($this->status, ['todo', 'in_progress', 'done'], true)) {
            $query->where('status', $this->status);
        }

        $tasks = $query->latest('created_at')->paginate($this->perPage)->withQueryString();

        return view('livewire.tasks.index', [
            'tasks' => $tasks,
            'users' => User::query()->select(['id', 'name', 'email'])->orderBy('name')->get(),
            'stats' => $stats,
            'deletingTask' => $this->deletingId ? Task::find($this->deletingId) : null,
        ]);
    }

    protected function resetForm(): void
    {
        $this->reset(['title', 'description', 'formStatus', 'formPriority', 'dueDate', 'assignee']);
        $this->resetErrorBag();
    }

    protected function statusLabel(string $status): string
    {
        return match ($status) {
            'done' => 'Done',
            'in_progress' => 'In Progress',
            default => 'To Do',
        };
    }
}
