<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_cannot_be_accessed_after_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout');

        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertRedirect('/login');
    }
}
