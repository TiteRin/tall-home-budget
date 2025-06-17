<?php

namespace App\Livewire;

use App\Models\Household;
use App\Models\HouseholdMember;
use Livewire\Component;

class HouseholdManager extends Component
{

    // Champs pour formulaire
    public int $householdId;
    public string $householdName = '';
    public bool $hasJointAccount = false;

    public array $householdMembers = [];

    // Champs temporaires pour ajout de membres
    public string $newMemberFirstName = '';
    public string $newMemberLastName = '';

    public function mount()
    {
        $household = Household::orderBy('created_at')->first();

        if (!$household) {
            $household = Household::create([
                'name' => 'Mon Foyer',
                'has_joint_account' => false,
            ]);
        }

        $this->householdId = $household->id;
        $this->householdName = $household->name ?? '';
        $this->hasJointAccount = $household->has_joint_account ?? false;
        $this->newMemberLastName = $household->name ?? '';

        $this->refreshMembers();
    }

    public function getHouseholdProperty(): ?Household 
    {
        return Household::find($this->householdId);
    }

    public function refreshMembers() 
    {
        $household = $this->household;
        $this->householdMembers = $household ? $household->members->toArray() : [];
    }

    public function save() {

        $this->validate([
            'householdName' => 'required|string|min:2'
        ]);

        $household = $this->household;
        $household->name = $this->householdName;
        $household->has_joint_account = $this->hasJointAccount; 
        $household->save();

        session()->flash('message', 'Foyer enregistré avec succès');
    }

    public function addMember() 
    {
        if (trim($this->newMemberFirstName) === '' || trim($this->newMemberLastName) === '') {
            session()->flash('error', 'Le prénom et le nom sont requis');
            return;
        }

        $household = $this->household;  

        $household->members()->create([
            'first_name' => $this->newMemberFirstName,
            'last_name' => $this->newMemberLastName,
        ]);

        $this->newMemberFirstName = '';
        $this->newMemberLastName = '';
        
        $this->refreshMembers();
    }

    public function removeMember($index) {

        $member = $this->householdMembers[$index] ?? null;

        if ($member && isset($member['id'])) {
            $household = $this->household;
            if ($household) {
                $household->members()->where('id', $member['id'])->delete();
            }
        }

        $this->refreshMembers();
    }

    public function render()
    {
        return view('livewire.household-manager');
    }

    public function updatedHouseholdName($value) {
        $this->newMemberLastName = $value;
    }
}
