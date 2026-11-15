<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Any authenticated member can browse the task board.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Any authenticated member can create tasks for the team.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Admins can manage every task; members can manage tasks they own or are assigned to.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->isAdmin()
            || $task->user_id === $user->id
            || $task->created_by === $user->id;
    }

    /**
     * Only admins or the task creator may delete a task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin() || $task->created_by === $user->id;
    }
}
