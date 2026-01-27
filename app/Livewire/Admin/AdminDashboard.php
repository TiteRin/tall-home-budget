<?php

namespace App\Livewire\Admin;

use App\Models\Household;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $member = $user->member;
        $household = $member?->household;

        $user->delete();

        if ($household) {
            // Check if there are other users in the household
            $otherUsersExist = User::whereHas('member', function ($query) use ($household) {
                $query->where('household_id', $household->id);
            })->exists();

            if (!$otherUsersExist) {
                // Delete all members and the household itself
                $household->members()->delete();
                $household->delete();
            }
        }

        session()->flash('message', 'Utilisateur supprimé avec succès.');
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard', [
            'users' => User::with('member.household')->latest()->get(),
            'households' => Household::withCount(['members', 'members as users_count' => function ($query) {
                $query->whereHas('user');
            }])->latest()->get(),
        ])->layout('layouts.app');
    }
}
