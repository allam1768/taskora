<?php

namespace App\Livewire\Team;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Daftar Anggota Tim - Taskora')]
class Members extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function updateMemberRole(int $userId, string $newRole): void
    {
        Gate::authorize('manage-members');

        if (! in_array($newRole, ['admin', 'member'], true)) {
            return;
        }

        $user = User::findOrFail($userId);
        $user->update(['role' => $newRole]);

        session()->flash('status', "Peran {$user->name} berhasil diubah menjadi {$newRole}.");
    }

    public function render()
    {
        $query = User::query()->select(['id', 'name', 'email', 'role', 'created_at']);

        if (! empty(trim($this->search))) {
            $keyword = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('email', 'like', $keyword);
            });
        }

        if (! empty($this->role)) {
            $query->where('role', $this->role);
        }

        $members = $query->latest('created_at')->paginate(10);

        return view('livewire.team.members', [
            'members' => $members,
        ]);
    }
}
