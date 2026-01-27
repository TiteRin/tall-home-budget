<?php

namespace App\Livewire;

use App\Models\Member;
use App\Models\User;
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
    public string $delete_confirm_password = '';

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

    public function deleteAccount()
    {
        $user = Auth::user();

        $this->validate([
            'delete_confirm_password' => ['required', 'current_password'],
        ]);

        $household = $user->member->household;

        // On vérifie s'il y a d'autres utilisateurs dans le foyer
        $otherUsersCount = User::whereHas('member', function ($query) use ($household) {
            $query->where('household_id', $household->id);
        })->where('users.id', '!=', $user->id)->count();

        $shouldDeleteEverything = ($otherUsersCount === 0);

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        if ($shouldDeleteEverything) {
            // Seul utilisateur, on supprime tout
            $household->bills()->delete();

            // On récupère les IDs des membres pour les supprimer après l'utilisateur
            $memberIds = $household->members()->pluck('id');

            // On supprime l'utilisateur d'abord car il a une clé étrangère vers member
            $user->delete();

            // Ensuite on supprime les membres et le foyer
            Member::whereIn('id', $memberIds)->delete();
            $household->delete();
        } else {
            $user->delete();
        }

        return redirect()->to('/');
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}
