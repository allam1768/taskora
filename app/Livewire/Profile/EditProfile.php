<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pengaturan Profil - Taskora')]
class EditProfile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $saved = false;

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
        ];
    }

    public function updateProfile(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];

        if (! empty($this->password)) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['required', 'string'];
        }

        $this->validate($rules);

        $user->name = trim($this->name);
        $user->email = Str::lower(trim($this->email));

        if (! empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        $this->password = '';
        $this->password_confirmation = '';
        $this->saved = true;

        session()->flash('status', 'Profil berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.profile.edit-profile');
    }
}
