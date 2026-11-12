<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $protectedRoutes = ['/dashboard', '/profile', '/team', '/tasks'];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }

        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create();

        $protectedRoutes = ['/dashboard', '/profile', '/team', '/tasks'];

        foreach ($protectedRoutes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_authenticated_user_is_redirected_away_from_guest_routes(): void
    {
        $user = User::factory()->create();

        $guestRoutes = ['/login', '/register'];

        foreach ($guestRoutes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertRedirect('/dashboard');
        }
    }
}
