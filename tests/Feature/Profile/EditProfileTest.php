<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\EditProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_can_be_rendered_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSeeLivewire(EditProfile::class);
    }

    public function test_unauthenticated_user_cannot_access_profile_page(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
            'email' => 'lama@taskora.test',
        ]);

        Livewire::actingAs($user)
            ->test(EditProfile::class)
            ->set('name', 'Nama Baru')
            ->set('email', 'baru@taskora.test')
            ->call('updateProfile')
            ->assertHasNoErrors()
            ->assertSee('Profil berhasil diperbarui');

        $user->refresh();
        $this->assertEquals('Nama Baru', $user->name);
        $this->assertEquals('baru@taskora.test', $user->email);
    }

    public function test_user_cannot_update_email_to_existing_another_user_email(): void
    {
        User::factory()->create([
            'email' => 'lain@taskora.test',
        ]);

        $user = User::factory()->create([
            'email' => 'saya@taskora.test',
        ]);

        Livewire::actingAs($user)
            ->test(EditProfile::class)
            ->set('email', 'lain@taskora.test')
            ->call('updateProfile')
            ->assertHasErrors(['email']);

        $user->refresh();
        $this->assertEquals('saya@taskora.test', $user->email);
    }

    public function test_password_remains_unchanged_when_password_fields_are_empty(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password-lama'),
        ]);

        Livewire::actingAs($user)
            ->test(EditProfile::class)
            ->set('name', 'Ganti Nama Saja')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertTrue(Hash::check('password-lama', $user->password));
    }

    public function test_password_can_be_updated_when_valid(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password-lama'),
        ]);

        Livewire::actingAs($user)
            ->test(EditProfile::class)
            ->set('password', 'password-baru123')
            ->set('password_confirmation', 'password-baru123')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertTrue(Hash::check('password-baru123', $user->password));
    }

    public function test_password_update_fails_when_confirmation_does_not_match(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password-lama'),
        ]);

        Livewire::actingAs($user)
            ->test(EditProfile::class)
            ->set('password', 'password-baru123')
            ->set('password_confirmation', 'salah-konfirmasi')
            ->call('updateProfile')
            ->assertHasErrors(['password']);

        $user->refresh();
        $this->assertTrue(Hash::check('password-lama', $user->password));
    }
}
