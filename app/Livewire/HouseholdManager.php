<?php

namespace App\Livewire;

use App\Models\Household;
use App\Models\HouseholdMember;
use Livewire\Component;

class HouseholdManager extends Component
{

    public Household $household;
    public array $householdMembers = [];

    public function mount()
    {
        $this->household = Household::firstOrNew();
        $this->householdMembers = $this->household->members->toArray();
    }

    public function save() {
        
        $this->household->save();

        $this->household->members()->delete(); // Simplification, on réécrit les personnes du foyer
        foreach ($this->householdMembers as $member) {
            $this->household->members()->create($member);
        }

        session()->flash('message', 'Foyer enregistré avec succès');
    }

    public function render()
    {
        return view('livewire.household-manager');
    }
}
