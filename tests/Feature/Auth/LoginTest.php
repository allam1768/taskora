<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSeeLivewire(Login::class);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'putra@taskora.test',
            'password' => bcrypt('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'putra@taskora.test')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'putra@taskora.test',
            'password' => bcrypt('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'putra@taskora.test')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_login_fails_with_unregistered_email(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'unknown@taskora.test')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/dashboard');
    }
}
