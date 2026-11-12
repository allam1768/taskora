<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TaskDataService
{
    /**
     * Determine if the tasks table exists in the database.
     */
    public function hasTasksTable(): bool
    {
        try {
            return Schema::hasTable('tasks');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get all tasks as a collection.
     *
     * @param  array{search?: string, status?: string, priority?: string, only_my?: bool}  $filters
     * @return Collection<int, object>
     */
    public function getTasks(array $filters = []): Collection
    {
        if ($this->hasTasksTable()) {
            return $this->getTasksFromDatabase($filters);
        }

        return $this->getTasksFromDummy($filters);
    }

    /**
     * Get paginated tasks.
     *
     * @param  array{search?: string, status?: string, priority?: string, only_my?: bool}  $filters
     */
    public function getPaginatedTasks(array $filters = [], int $perPage = 6, int $page = 1): LengthAwarePaginator
    {
        $all = $this->getTasks($filters);
        $total = $all->count();
        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Get task statistics (Total, To Do, In Progress, Done, Progress percentage).
     *
     * @return array{total: int, todo: int, in_progress: int, done: int, progress: int}
     */
    public function getStatistics(?int $userId = null, bool $onlyMy = false): array
    {
        $filters = $onlyMy && $userId ? ['user_id' => $userId] : [];
        $tasks = $this->getTasks($filters);

        $total = $tasks->count();
        $todo = $tasks->filter(fn ($t) => in_array(strtolower($t->status), ['todo', 'to do'], true))->count();
        $inProgress = $tasks->filter(fn ($t) => in_array(strtolower($t->status), ['in_progress', 'in progress'], true))->count();
        $done = $tasks->filter(fn ($t) => in_array(strtolower($t->status), ['done', 'completed'], true))->count();

        $progress = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        return [
            'total' => $total,
            'todo' => $todo,
            'in_progress' => $inProgress,
            'done' => $done,
            'progress' => $progress,
        ];
    }

    /**
     * Get recent tasks (latest created or updated).
     *
     * @return Collection<int, object>
     */
    public function getRecentTasks(int $limit = 5): Collection
    {
        return $this->getTasks([])
            ->sortByDesc(fn ($t) => $t->created_at ?? $t->id)
            ->take($limit)
            ->values();
    }

    /**
     * Get upcoming deadline tasks (pending tasks due soonest).
     *
     * @return Collection<int, object>
     */
    public function getUpcomingDeadlines(int $limit = 3): Collection
    {
        return $this->getTasks([])
            ->filter(fn ($t) => ! in_array(strtolower($t->status), ['done', 'completed'], true) && ! empty($t->due_date))
            ->sortBy(fn ($t) => Carbon::parse($t->due_date)->timestamp)
            ->take($limit)
            ->values();
    }

    /**
     * Fetch tasks directly from the database table.
     *
     * @param  array{search?: string, status?: string, priority?: string, only_my?: bool, user_id?: int}  $filters
     * @return Collection<int, object>
     */
    protected function getTasksFromDatabase(array $filters = []): Collection
    {
        $query = DB::table('tasks');

        // Check if users table can be joined for assigned user
        $hasUsers = Schema::hasTable('users');
        $hasUserIdCol = Schema::hasColumn('tasks', 'user_id');
        $hasAssignedToCol = Schema::hasColumn('tasks', 'assigned_to');

        if ($hasUsers && ($hasUserIdCol || $hasAssignedToCol)) {
            $userCol = $hasUserIdCol ? 'tasks.user_id' : 'tasks.assigned_to';
            $query->leftJoin('users', $userCol, '=', 'users.id')
                ->select([
                    'tasks.*',
                    'users.name as assigned_user_name',
                    'users.email as assigned_user_email',
                ]);
        } else {
            $query->select('tasks.*');
        }

        if (! empty($filters['user_id'])) {
            $userCol = $hasUserIdCol ? 'tasks.user_id' : 'tasks.assigned_to';
            $query->where($userCol, $filters['user_id']);
        }

        if (! empty($filters['only_my']) && Auth::check()) {
            $userCol = $hasUserIdCol ? 'tasks.user_id' : 'tasks.assigned_to';
            $query->where($userCol, Auth::id());
        }

        if (! empty($filters['search'])) {
            $keyword = '%'.trim($filters['search']).'%';
            $titleCol = Schema::hasColumn('tasks', 'title') ? 'title' : (Schema::hasColumn('tasks', 'name') ? 'name' : null);
            $descCol = Schema::hasColumn('tasks', 'description') ? 'description' : null;

            if ($titleCol) {
                $query->where(function ($q) use ($titleCol, $descCol, $keyword) {
                    $q->where("tasks.{$titleCol}", 'like', $keyword);
                    if ($descCol) {
                        $q->orWhere("tasks.{$descCol}", 'like', $keyword);
                    }
                });
            }
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $statusVal = $filters['status'];
            $query->where(function ($q) use ($statusVal) {
                $q->where('tasks.status', $statusVal)
                    ->orWhere('tasks.status', str_replace('_', ' ', $statusVal))
                    ->orWhere('tasks.status', ucwords(str_replace('_', ' ', $statusVal)));
            });
        }

        if (! empty($filters['priority']) && $filters['priority'] !== 'all') {
            $priorityVal = $filters['priority'];
            $query->where(function ($q) use ($priorityVal) {
                $q->where('tasks.priority', $priorityVal)
                    ->orWhere('tasks.priority', ucfirst($priorityVal));
            });
        }

        $orderCol = Schema::hasColumn('tasks', 'created_at') ? 'tasks.created_at' : 'tasks.id';

        return $query->orderByDesc($orderCol)->get()->map(function ($item) {
            return $this->normalizeTaskObject($item);
        });
    }

    /**
     * Generate structured dummy tasks with realistic assignments.
     *
     * @param  array{search?: string, status?: string, priority?: string, only_my?: bool, user_id?: int}  $filters
     * @return Collection<int, object>
     */
    protected function getTasksFromDummy(array $filters = []): Collection
    {
        $users = User::all();
        $firstUser = $users->first();
        $secondUser = $users->skip(1)->first() ?? $firstUser;

        $now = Carbon::now();

        $rawTasks = [
            [
                'id' => 1,
                'title' => 'Design System & Navigation Shell',
                'description' => 'Build modern sidebar navigation, top header, and responsive layout for Taskora.',
                'status' => 'Done',
                'priority' => 'High',
                'due_date' => $now->copy()->addDays(1)->format('Y-m-d'),
                'user_id' => $firstUser?->id ?? 1,
                'assigned_user_name' => $firstUser?->name ?? 'Admin Taskora',
                'assigned_user_email' => $firstUser?->email ?? 'admin@taskora.test',
                'created_at' => $now->copy()->subDays(5)->toDateTimeString(),
            ],
            [
                'id' => 2,
                'title' => 'Authentication & Role Gate Integration',
                'description' => 'Implement secure authentication, session handling, and member/admin role gates.',
                'status' => 'Done',
                'priority' => 'High',
                'due_date' => $now->copy()->subDays(1)->format('Y-m-d'),
                'user_id' => $secondUser?->id ?? 2,
                'assigned_user_name' => $secondUser?->name ?? 'Member Taskora',
                'assigned_user_email' => $secondUser?->email ?? 'member@taskora.test',
                'created_at' => $now->copy()->subDays(4)->toDateTimeString(),
            ],
            [
                'id' => 3,
                'title' => 'Interactive Dashboard Statistics & Metrics',
                'description' => 'Create real-time metric cards for Total, To Do, In Progress, and Completed tasks.',
                'status' => 'Done',
                'priority' => 'Medium',
                'due_date' => $now->copy()->addDays(2)->format('Y-m-d'),
                'user_id' => $firstUser?->id ?? 1,
                'assigned_user_name' => $firstUser?->name ?? 'Admin Taskora',
                'assigned_user_email' => $firstUser?->email ?? 'admin@taskora.test',
                'created_at' => $now->copy()->subDays(3)->toDateTimeString(),
            ],
            [
                'id' => 4,
                'title' => 'Realtime Task Search & Filter System',
                'description' => 'Implement Livewire reactive search by title with status and priority multi-filtering.',
                'status' => 'In Progress',
                'priority' => 'High',
                'due_date' => $now->copy()->addDays(3)->format('Y-m-d'),
                'user_id' => $firstUser?->id ?? 1,
                'assigned_user_name' => $firstUser?->name ?? 'Admin Taskora',
                'assigned_user_email' => $firstUser?->email ?? 'admin@taskora.test',
                'created_at' => $now->copy()->subDays(2)->toDateTimeString(),
            ],
            [
                'id' => 5,
                'title' => 'Database Schema Migration for Tasks',
                'description' => 'Finalize task table columns, foreign keys to users, status enums, and deadlines.',
                'status' => 'In Progress',
                'priority' => 'High',
                'due_date' => $now->copy()->addDays(4)->format('Y-m-d'),
                'user_id' => $secondUser?->id ?? 2,
                'assigned_user_name' => $secondUser?->name ?? 'Member Taskora',
                'assigned_user_email' => $secondUser?->email ?? 'member@taskora.test',
                'created_at' => $now->copy()->subDays(2)->toDateTimeString(),
            ],
            [
                'id' => 6,
                'title' => 'Deadline Reminder & Alert Notifications',
                'description' => 'Add visual badges for upcoming deadlines and notify team members before due dates.',
                'status' => 'In Progress',
                'priority' => 'Medium',
                'due_date' => $now->copy()->addDays(5)->format('Y-m-d'),
                'user_id' => $firstUser?->id ?? 1,
                'assigned_user_name' => $firstUser?->name ?? 'Admin Taskora',
                'assigned_user_email' => $firstUser?->email ?? 'admin@taskora.test',
                'created_at' => $now->copy()->subDays(1)->toDateTimeString(),
            ],
            [
                'id' => 7,
                'title' => 'Task Priority Sorting & Tagging',
                'description' => 'Support sorting tasks by priority (High, Medium, Low) and custom tags.',
                'status' => 'In Progress',
                'priority' => 'Low',
                'due_date' => $now->copy()->addDays(6)->format('Y-m-d'),
                'user_id' => $secondUser?->id ?? 2,
                'assigned_user_name' => $secondUser?->name ?? 'Member Taskora',
                'assigned_user_email' => $secondUser?->email ?? 'member@taskora.test',
                'created_at' => $now->copy()->subHours(18)->toDateTimeString(),
            ],
            [
                'id' => 8,
                'title' => 'Team Collaboration Comments Thread',
                'description' => 'Enable team discussions and comment history directly on task detail views.',
                'status' => 'To Do',
                'priority' => 'Medium',
                'due_date' => $now->copy()->addDays(7)->format('Y-m-d'),
                'user_id' => $secondUser?->id ?? 2,
                'assigned_user_name' => $secondUser?->name ?? 'Member Taskora',
                'assigned_user_email' => $secondUser?->email ?? 'member@taskora.test',
                'created_at' => $now->copy()->subHours(12)->toDateTimeString(),
            ],
            [
                'id' => 9,
                'title' => 'File Attachments & Asset Uploads',
                'description' => 'Allow members to upload screenshots, documents, and specifications to tasks.',
                'status' => 'To Do',
                'priority' => 'Low',
                'due_date' => $now->copy()->addDays(9)->format('Y-m-d'),
                'user_id' => $firstUser?->id ?? 1,
                'assigned_user_name' => $firstUser?->name ?? 'Admin Taskora',
                'assigned_user_email' => $firstUser?->email ?? 'admin@taskora.test',
                'created_at' => $now->copy()->subHours(8)->toDateTimeString(),
            ],
            [
                'id' => 10,
                'title' => 'Export Task Reports to PDF and CSV',
                'description' => 'Generate downloadable summary reports of team productivity and completion rate.',
                'status' => 'To Do',
                'priority' => 'Low',
                'due_date' => $now->copy()->addDays(12)->format('Y-m-d'),
                'user_id' => $secondUser?->id ?? 2,
                'assigned_user_name' => $secondUser?->name ?? 'Member Taskora',
                'assigned_user_email' => $secondUser?->email ?? 'member@taskora.test',
                'created_at' => $now->copy()->subHours(4)->toDateTimeString(),
            ],
            [
                'id' => 11,
                'title' => 'Mobile Swipe Gestures for Status Update',
                'description' => 'Enhance touch experience on smartphones for rapid status transitioning.',
                'status' => 'To Do',
                'priority' => 'Medium',
                'due_date' => $now->copy()->addDays(14)->format('Y-m-d'),
                'user_id' => $firstUser?->id ?? 1,
                'assigned_user_name' => $firstUser?->name ?? 'Admin Taskora',
                'assigned_user_email' => $firstUser?->email ?? 'admin@taskora.test',
                'created_at' => $now->copy()->subHours(2)->toDateTimeString(),
            ],
            [
                'id' => 12,
                'title' => 'Automated CI/CD Deployment Pipeline',
                'description' => 'Configure GitHub actions workflow to run tests, pint formatting, and staging build.',
                'status' => 'Done',
                'priority' => 'High',
                'due_date' => $now->copy()->subDays(2)->format('Y-m-d'),
                'user_id' => $firstUser?->id ?? 1,
                'assigned_user_name' => $firstUser?->name ?? 'Admin Taskora',
                'assigned_user_email' => $firstUser?->email ?? 'admin@taskora.test',
                'created_at' => $now->copy()->subDays(6)->toDateTimeString(),
            ],
        ];

        $collection = collect($rawTasks)->map(fn ($item) => (object) $item);

        if (! empty($filters['user_id'])) {
            $collection = $collection->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['only_my']) && Auth::check()) {
            $collection = $collection->where('user_id', Auth::id());
        }

        if (! empty($filters['search'])) {
            $search = strtolower(trim($filters['search']));
            $collection = $collection->filter(function ($task) use ($search) {
                return str_contains(strtolower($task->title), $search)
                    || str_contains(strtolower($task->description ?? ''), $search);
            });
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $statusVal = str_replace(['_', ' '], '', strtolower($filters['status']));
            $collection = $collection->filter(function ($task) use ($statusVal) {
                $itemStatus = str_replace(['_', ' '], '', strtolower($task->status));

                return $itemStatus === $statusVal;
            });
        }

        if (! empty($filters['priority']) && $filters['priority'] !== 'all') {
            $priorityVal = strtolower($filters['priority']);
            $collection = $collection->filter(function ($task) use ($priorityVal) {
                return strtolower($task->priority) === $priorityVal;
            });
        }

        return $collection->values();
    }

    /**
     * Normalize task object properties across different schema variations.
     */
    protected function normalizeTaskObject(object $item): object
    {
        $normalized = clone $item;

        $normalized->title = $item->title ?? $item->name ?? 'Untitled Task';
        $normalized->description = $item->description ?? '';
        $normalized->status = $this->formatStatus($item->status ?? 'To Do');
        $normalized->priority = $this->formatPriority($item->priority ?? 'Medium');
        $normalized->due_date = $item->due_date ?? $item->deadline ?? null;
        $normalized->assigned_user_name = $item->assigned_user_name ?? (isset($item->user_id) ? (User::find($item->user_id)?->name ?? 'Unassigned') : 'Unassigned');

        return $normalized;
    }

    protected function formatStatus(string $status): string
    {
        $cleaned = strtolower(trim($status));

        return match ($cleaned) {
            'done', 'completed' => 'Done',
            'in_progress', 'in progress' => 'In Progress',
            default => 'To Do',
        };
    }

    protected function formatPriority(string $priority): string
    {
        $cleaned = strtolower(trim($priority));

        return match ($cleaned) {
            'high' => 'High',
            'low' => 'Low',
            default => 'Medium',
        };
    }
}
