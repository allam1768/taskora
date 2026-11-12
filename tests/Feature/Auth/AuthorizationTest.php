<?php

namespace Tests\Feature\Auth;

use App\Livewire\Team\Members;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_management_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/management');
        $response->assertStatus(200);
    }

    public function test_member_cannot_access_admin_management_route(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)->get('/admin/management');
        $response->assertStatus(403);
    }

    public function test_admin_can_update_member_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['role' => 'member']);

        Livewire::actingAs($admin)
            ->test(Members::class)
            ->call('updateMemberRole', $targetUser->id, 'admin')
            ->assertHasNoErrors();

        $targetUser->refresh();
        $this->assertEquals('admin', $targetUser->role);
    }

    public function test_member_is_forbidden_from_updating_member_role(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $targetUser = User::factory()->create(['role' => 'member']);

        Livewire::actingAs($member)
            ->test(Members::class)
            ->call('updateMemberRole', $targetUser->id, 'admin')
            ->assertForbidden();

        $targetUser->refresh();
        $this->assertEquals('member', $targetUser->role);
    }

    public function test_update_profile_gate_authorizes_only_own_profile(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertTrue(Gate::forUser($user1)->allows('update-profile', $user1));
        $this->assertFalse(Gate::forUser($user1)->allows('update-profile', $user2));
    }
}
