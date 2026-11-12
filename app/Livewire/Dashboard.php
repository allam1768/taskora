<?php

namespace App\Livewire;

use App\Services\TaskDataService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Dashboard - Taskora')]
class Dashboard extends Component
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

    public function mount(): void
    {
        if (request()->query('scope') === 'my' || request()->query('my') === '1') {
            $this->onlyMy = true;
        }
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

    public function render(TaskDataService $taskService)
    {
        $user = Auth::user();

        $filters = [
            'search' => $this->search,
            'status' => $this->status,
            'priority' => $this->priority,
            'only_my' => $this->onlyMy,
            'user_id' => $this->onlyMy && $user ? $user->id : null,
        ];

        $statistics = $taskService->getStatistics($user?->id, $this->onlyMy);
        $paginatedTasks = $taskService->getPaginatedTasks($filters, perPage: 6, page: $this->getPage());
        $recentTasks = $taskService->getRecentTasks(limit: 5);
        $upcomingDeadlines = $taskService->getUpcomingDeadlines(limit: 3);

        return view('livewire.dashboard', [
            'user' => $user,
            'statistics' => $statistics,
            'tasks' => $paginatedTasks,
            'recentTasks' => $recentTasks,
            'upcomingDeadlines' => $upcomingDeadlines,
            'isDbConnected' => $taskService->hasTasksTable(),
        ]);
    }
}
