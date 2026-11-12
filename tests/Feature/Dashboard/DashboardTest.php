<?php

namespace Tests\Feature\Dashboard;

use App\Livewire\Dashboard;
use App\Models\User;
use App\Services\TaskDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['name' => 'Putra']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Dashboard::class);
        $response->assertSee('Welcome back, Putra!');
        $response->assertSee('Taskora');
        $response->assertSee('Organize Work. Achieve More.');
    }

    public function test_dashboard_sidebar_contains_all_required_navigation_links(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('All Tasks');
        $response->assertSee('My Tasks');
        $response->assertSee('Team Members');
        $response->assertSee('Profile');
        $response->assertSee('Keluar (Logout)');
    }

    public function test_dashboard_renders_statistics_cards(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('Total Tasks')
            ->assertSee('To Do')
            ->assertSee('In Progress')
            ->assertSee('Done')
            ->assertSee('Overall Work Progress');
    }

    public function test_dashboard_progress_calculation(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)->test(Dashboard::class);

        $stats = $component->viewData('statistics');

        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('todo', $stats);
        $this->assertArrayHasKey('in_progress', $stats);
        $this->assertArrayHasKey('done', $stats);
        $this->assertArrayHasKey('progress', $stats);

        if ($stats['total'] > 0) {
            $expectedProgress = (int) round(($stats['done'] / $stats['total']) * 100);
            $this->assertEquals($expectedProgress, $stats['progress']);
        } else {
            $this->assertEquals(0, $stats['progress']);
        }
    }

    public function test_dashboard_search_filters_tasks_by_title(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('search', 'Design System')
            ->assertSee('Design System')
            ->assertDontSee('Automated CI/CD');
    }

    public function test_dashboard_filter_by_status(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('status', 'todo')
            ->assertSet('status', 'todo')
            ->call('setStatus', 'done')
            ->assertSet('status', 'done');
    }

    public function test_dashboard_filter_by_priority(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('priority', 'high')
            ->assertSet('priority', 'high')
            ->call('setPriority', 'low')
            ->assertSet('priority', 'low');
    }

    public function test_dashboard_reset_filters(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('search', 'Design')
            ->set('status', 'done')
            ->set('priority', 'high')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('status', 'all')
            ->assertSet('priority', 'all');
    }

    public function test_dashboard_shows_empty_state_when_no_tasks_match(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('search', 'xyznonexistentquery999')
            ->assertSee('No tasks available');
    }

    public function test_dashboard_shows_upcoming_deadlines_section(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('Upcoming Deadlines');
    }

    public function test_tasks_route_renders_dashboard_view(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/tasks');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Dashboard::class);
    }

    public function test_my_tasks_scope_activates_personal_filter(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->withQueryParams(['scope' => 'my'])
            ->test(Dashboard::class)
            ->assertSet('onlyMy', true);
    }

    public function test_task_data_service_seamlessly_integrates_with_database_when_table_exists(): void
    {
        $user = User::factory()->create(['name' => 'Database User']);

        Schema::create('tasks', function ($table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('To Do');
            $table->string('priority')->default('Medium');
            $table->date('due_date')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });

        DB::table('tasks')->insert([
            [
                'title' => 'Real Database Task 1',
                'description' => 'Direct from SQLite/MySQL table',
                'status' => 'Done',
                'priority' => 'High',
                'due_date' => now()->addDays(2)->toDateString(),
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Real Database Task 2',
                'description' => 'Another database task',
                'status' => 'To Do',
                'priority' => 'Low',
                'due_date' => now()->addDays(5)->toDateString(),
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $service = new TaskDataService;
        $this->assertTrue($service->hasTasksTable());

        $stats = $service->getStatistics();
        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(1, $stats['done']);
        $this->assertEquals(1, $stats['todo']);
        $this->assertEquals(50, $stats['progress']);

        $tasks = $service->getTasks();
        $this->assertCount(2, $tasks);
        $this->assertEquals('Real Database Task 1', $tasks->first()->title);

        Schema::dropIfExists('tasks');
    }
}
