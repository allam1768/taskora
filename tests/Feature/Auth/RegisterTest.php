<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSeeLivewire(Register::class);
    }

    public function test_new_users_can_register(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Putra Permata')
            ->set('email', 'putra@taskora.test')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        $user = User::where('email', 'putra@taskora.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Putra Permata', $user->name);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_registration_fails_if_email_already_exists(): void
    {
        User::factory()->create([
            'email' => 'putra@taskora.test',
        ]);

        Livewire::test(Register::class)
            ->set('name', 'Putra Permata')
            ->set('email', 'putra@taskora.test')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_registration_fails_if_passwords_do_not_match(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Putra Permata')
            ->set('email', 'putra@taskora.test')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different-password')
            ->call('register')
            ->assertHasErrors(['password']);

        $this->assertGuest();
    }

    public function test_registration_validation_fails_on_empty_fields(): void
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', '')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('register')
            ->assertHasErrors(['name', 'email', 'password', 'password_confirmation']);

        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_access_register_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect('/dashboard');
    }
}
