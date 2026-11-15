<?php

namespace Tests\Feature\Tasks;

use App\Livewire\Tasks\Index;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_tasks(): void
    {
        $this->get('/tasks')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_task_management(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/tasks');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Index::class);
        $response->assertSee('Tasks');
    }

    public function test_admin_can_create_a_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $assignee = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('openCreate')
            ->set('title', 'Implement Login Flow')
            ->set('description', 'Build the login form and session handling.')
            ->set('formStatus', 'todo')
            ->set('formPriority', 'high')
            ->set('dueDate', now()->addDays(3)->toDateString())
            ->set('assignee', (string) $assignee->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Implement Login Flow',
            'status' => 'todo',
            'priority' => 'high',
            'user_id' => $assignee->id,
            'created_by' => $admin->id,
        ]);
    }

    public function test_task_title_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title' => 'required']);
    }

    public function test_task_status_must_be_valid(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('title', 'Valid Title')
            ->set('formStatus', 'archived')
            ->call('save')
            ->assertHasErrors(['formStatus' => 'in']);
    }

    public function test_admin_can_update_any_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $task = Task::factory()->create(['title' => 'Old Title']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('openEdit', $task->id)
            ->set('title', 'Updated Title')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_member_cannot_update_a_task_owned_by_someone_else(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        Livewire::actingAs($other)
            ->test(Index::class)
            ->call('openEdit', $task->id)
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $task->title,
        ]);
    }

    public function test_member_can_update_a_task_assigned_to_them(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $assignee->id,
            'created_by' => $creator->id,
        ]);

        Livewire::actingAs($assignee)
            ->test(Index::class)
            ->call('openEdit', $task->id)
            ->set('formStatus', 'in_progress')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_admin_can_delete_a_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $task = Task::factory()->create();

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('confirmDelete', $task->id)
            ->call('deleteTask')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_member_cannot_delete_a_task_created_by_someone_else(): void
    {
        $creator = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $other->id,
            'created_by' => $creator->id,
        ]);

        Livewire::actingAs($other)
            ->test(Index::class)
            ->call('confirmDelete', $task->id)
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_status_can_be_cycled_forward(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->todo()->create([
            'user_id' => $user->id,
            'created_by' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('cycleStatus', $task->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_filter_by_status_only_shows_matching_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->done()->create(['title' => 'Completed Deployment']);
        Task::factory()->todo()->create(['title' => 'Pending Research']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('status', 'done')
            ->assertSee('Completed Deployment')
            ->assertDontSee('Pending Research');
    }

    public function test_my_tasks_scope_only_shows_tasks_assigned_to_current_user(): void
    {
        $user = User::factory()->create();
        Task::factory()->create([
            'title' => 'My Own Task',
            'user_id' => $user->id,
            'created_by' => $user->id,
        ]);
        Task::factory()->create([
            'title' => 'Someone Else Task',
            'user_id' => User::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
        ]);

        Livewire::actingAs($user)
            ->withQueryParams(['scope' => 'my'])
            ->test(Index::class)
            ->assertSet('onlyMy', true)
            ->assertSee('My Own Task')
            ->assertDontSee('Someone Else Task');
    }

    public function test_search_filters_tasks_by_title(): void
    {
        $user = User::factory()->create();
        Task::factory()->create(['title' => 'Deploy Production Server']);
        Task::factory()->create(['title' => 'Write Documentation']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('search', 'Deploy')
            ->assertSee('Deploy Production Server')
            ->assertDontSee('Write Documentation');
    }

    public function test_filter_by_priority_only_shows_matching_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->high()->create(['title' => 'Urgent Bug Fix']);
        Task::factory()->low()->create(['title' => 'Nice To Have Polish']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('priority', 'high')
            ->assertSee('Urgent Bug Fix')
            ->assertDontSee('Nice To Have Polish');
    }
}
