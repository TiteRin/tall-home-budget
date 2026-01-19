<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class UserProfile extends Component
{
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->email = $user->email;
    }

    public function updateEmail()
    {
        $user = Auth::user();

        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'email' => $this->email,
        ]);

        session()->flash('email_message', 'Adresse e-mail mise à jour avec succès.');
    }

    public function updatePassword()
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('password_message', 'Mot de passe mis à jour avec succès.');
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}
