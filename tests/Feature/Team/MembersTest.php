<?php

namespace Tests\Feature\Team;

use App\Livewire\Team\Members;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_members_page_can_be_rendered_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/team');
        $response->assertStatus(200);
        $response->assertSeeLivewire(Members::class);
    }

    public function test_unauthenticated_user_cannot_access_team_members_page(): void
    {
        $response = $this->get('/team');
        $response->assertRedirect('/login');
    }

    public function test_team_members_are_listed_correctly(): void
    {
        $user = User::factory()->create(['name' => 'Alice Admin', 'role' => 'admin']);
        $member = User::factory()->create(['name' => 'Bob Member', 'role' => 'member']);

        Livewire::actingAs($user)
            ->test(Members::class)
            ->assertSee('Alice Admin')
            ->assertSee('Bob Member')
            ->assertSee('Admin')
            ->assertSee('Member');
    }

    public function test_search_filters_team_members_by_name_or_email(): void
    {
        $user = User::factory()->create(['name' => 'John Doe', 'email' => 'john@taskora.test']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@taskora.test']);

        Livewire::actingAs($user)
            ->test(Members::class)
            ->set('search', 'Jane')
            ->assertSee('Jane Smith')
            ->assertDontSee('John Doe')
            ->set('search', 'john@taskora.test')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_filter_by_role_works_properly(): void
    {
        $admin = User::factory()->create(['name' => 'Charlie Admin', 'role' => 'admin']);
        User::factory()->create(['name' => 'David Member', 'role' => 'member']);

        Livewire::actingAs($admin)
            ->test(Members::class)
            ->set('role', 'admin')
            ->assertSee('Charlie Admin')
            ->assertDontSee('David Member')
            ->set('role', 'member')
            ->assertSee('David Member')
            ->assertDontSee('Charlie Admin');
    }

    public function test_pagination_works_when_many_members_exist(): void
    {
        $user = User::factory()->create(['name' => 'Root User']);
        User::factory()->count(15)->create();

        $component = Livewire::actingAs($user)
            ->test(Members::class);

        $this->assertCount(10, $component->viewData('members'));
    }
}
