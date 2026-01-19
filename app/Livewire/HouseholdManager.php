<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Support\Facades\Auth;
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

    // Champs pour l'édition de membre
    public ?int $editingMemberId = null;
    public string $editingMemberFirstName = '';
    public string $editingMemberLastName = '';

    // Champs pour la suppression de membre
    public ?int $memberIdToDelete = null;
    public int $impactedBillsCount = 0;
    public string $deleteAction = 'reassign'; // 'reassign' ou 'delete_bills'
    public string $reassignmentTarget = ''; // Nom du membre ou 'compte joint'

    // Champs temporaires pour ajout de membres
    public string $newMemberFirstName = '';
    public string $newMemberLastName = '';

    public function mount(CurrentHouseholdServiceContract $currentHouseholdService)
    {
        if (Auth::guest()) {
            return $this->redirect(route('home'));
        }

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

        $memberData = $this->householdMembers[$index] ?? null;

        if (!$memberData || isset($memberData['user'])) {
            return;
        }

        $member = \App\Models\Member::find($memberData['id']);
        if (!$member) return;

        $this->memberIdToDelete = $member->id;
        $this->impactedBillsCount = $member->bills()->count();
        $this->deleteAction = 'reassign';

        if ($this->hasJointAccount) {
            $this->reassignmentTarget = 'compte joint';
        } else {
            $firstOtherMember = $this->household->members()
                ->where('id', '!=', $member->id)
                ->first();
            $this->reassignmentTarget = $firstOtherMember ? $firstOtherMember->full_name : 'aucun (suppression forcée)';
        }

        $this->dispatch('open-modal', 'confirm-delete-member');
    }

    public function performDelete()
    {
        if (!$this->memberIdToDelete) return;

        $member = \App\Models\Member::find($this->memberIdToDelete);
        if (!$member || $member->hasUserAccount()) {
            $this->memberIdToDelete = null;
            return;
        }

        if ($this->impactedBillsCount > 0) {
            if ($this->deleteAction === 'delete_bills') {
                $member->bills()->delete();
            } else {
                // Reassign
                $targetMemberId = null;
                if (!$this->hasJointAccount) {
                    $firstOtherMember = $this->household->members()
                        ->where('id', '!=', $member->id)
                        ->first();
                    $targetMemberId = $firstOtherMember?->id;
                }

                $member->bills()->update(['member_id' => $targetMemberId]);
            }
        }

        $member->delete();

        $this->memberIdToDelete = null;
        $this->refreshMembers();
        $this->dispatch('close-modal', 'confirm-delete-member');
        session()->flash('message', 'Membre supprimé avec succès');
    }

    public function editMember($index)
    {
        $memberData = $this->householdMembers[$index] ?? null;

        if (!$memberData) {
            return;
        }

        $hasUser = isset($memberData['user']) && $memberData['user'] !== null;
        $isCurrentUser = $memberData['id'] === Auth::user()->member_id;

        $canEdit = !$hasUser || $isCurrentUser;

        if ($canEdit) {
            $this->editingMemberId = $memberData['id'];
            $this->editingMemberFirstName = $memberData['first_name'];
            $this->editingMemberLastName = $memberData['last_name'];
        }
    }

    public function updateMember()
    {
        $this->validate([
            'editingMemberFirstName' => 'required|string|min:2',
            'editingMemberLastName' => 'required|string|min:2',
        ]);

        $member = \App\Models\Member::find($this->editingMemberId);

        if (!$member) {
            $this->cancelEdit();
            return;
        }

        $canUpdate = !$member->hasUserAccount() || $member->id === Auth::user()->member_id;

        if ($canUpdate) {
            $member->update([
                'first_name' => $this->editingMemberFirstName,
                'last_name' => $this->editingMemberLastName,
            ]);
        }

        $this->cancelEdit();
        $this->refreshMembers();
    }

    public function cancelEdit()
    {
        $this->editingMemberId = null;
        $this->editingMemberFirstName = '';
        $this->editingMemberLastName = '';
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
