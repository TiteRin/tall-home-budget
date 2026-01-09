<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Services\Household\CurrentHouseholdService;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class HouseholdManager extends Component
{

    // Champs pour formulaire
    public int $householdId;
    public string $householdName = '';
    public bool $hasJointAccount = false;
    public string $defaultDistributionMethod = DistributionMethod::EQUAL->value;

    public array $householdMembers = [];

    // Champs temporaires pour ajout de membres
    public string $newMemberFirstName = '';
    public string $newMemberLastName = '';

    public function mount(CurrentHouseholdServiceContract $currentHouseholdService)
    {
        $household = $currentHouseholdService->getCurrentHousehold();

        $this->householdId = $household->id;
        $this->householdName = $household->name ?? '';
        $this->hasJointAccount = $household->has_joint_account;
        $this->defaultDistributionMethod = $household->getDefaultDistributionMethod()->value;
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
        $this->householdMembers = $household ? $household->members()->with('user')->get()->toArray() : [];
    }

    public function getInviteLink($memberId) {
        return URL::temporarySignedRoute('register', now()->addDays(7), ['member_id' => $memberId]);
    }

    public function save() {

        $this->validate([
            'householdName' => 'required|string|min:2'
        ]);

        $household = $this->household;
        $household->name = $this->householdName;
        $household->has_joint_account = filter_var($this->hasJointAccount, FILTER_VALIDATE_BOOLEAN);
        $household->default_distribution_method = DistributionMethod::from($this->defaultDistributionMethod);

        $household->save();

        session()->flash('message', 'Foyer enregistré avec succès');
    }

    public function addMember()
    {
        $this->validate([
            'newMemberFirstName' => 'required|string|min:2',
            'newMemberLastName' => 'required|string|min:2',
        ]);

        $household = $this->household;

        $household->members()->create([
            'first_name' => $this->newMemberFirstName,
            'last_name' => $this->newMemberLastName,
        ]);

        $this->newMemberFirstName = '';
        $this->newMemberLastName = $household->name ?? '';

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

    public function getDistributionMethodOptionsProperty(): array
    {
        return DistributionMethod::options();
    }

    public function render()
    {
        return view('livewire.household-manager');
    }

    public function updatedHouseholdName($value) {
        $this->newMemberLastName = $value;
    }
}
