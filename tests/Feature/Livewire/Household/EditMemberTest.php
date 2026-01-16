<?php

namespace Tests\Feature\Livewire\Household;

use App\Livewire\HouseholdManager;
use App\Models\Household;
use App\Models\Member;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('Member editing', function () {
    beforeEach(function () {
        $this->household = Household::factory()->create(['name' => 'Famille Test']);
        // Membre sans utilisateur
        $this->member = Member::factory()->create([
            'household_id' => $this->household->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont'
        ]);
        // Membre avec utilisateur
        $this->memberWithUser = Member::factory()->create([
            'household_id' => $this->household->id,
            'first_name' => 'Marie',
            'last_name' => 'Curie'
        ]);
        $this->user = UserFactory::new()->create(['member_id' => $this->memberWithUser->id]);

        $this->actingAs($this->user);
    });

    test("should start editing a member without user account", function () {
        Livewire::test(HouseholdManager::class)
            ->call('editMember', 0) // Index de Jean Dupont
            ->assertSet('editingMemberId', $this->member->id)
            ->assertSet('editingMemberFirstName', 'Jean')
            ->assertSet('editingMemberLastName', 'Dupont');
    });

    test("should not start editing a member with user account", function () {
        Livewire::test(HouseholdManager::class)
            ->call('editMember', 1) // Index de Marie Curie
            ->assertSet('editingMemberId', null);
    });

    test("should update member with valid data", function () {
        Livewire::test(HouseholdManager::class)
            ->call('editMember', 0)
            ->set('editingMemberFirstName', 'Jacques')
            ->set('editingMemberLastName', 'Martin')
            ->call('updateMember')
            ->assertSet('editingMemberId', null)
            ->assertCount('householdMembers', 2);

        $this->member->refresh();
        expect($this->member->first_name)->toBe('Jacques')
            ->and($this->member->last_name)->toBe('Martin');
    });

    test("should validate member data on update", function () {
        Livewire::test(HouseholdManager::class)
            ->call('editMember', 0)
            ->set('editingMemberFirstName', '')
            ->call('updateMember')
            ->assertHasErrors(['editingMemberFirstName' => 'required']);
    });

    test("should cancel editing", function () {
        Livewire::test(HouseholdManager::class)
            ->call('editMember', 0)
            ->call('cancelEdit')
            ->assertSet('editingMemberId', null)
            ->assertSet('editingMemberFirstName', '')
            ->assertSet('editingMemberLastName', '');
    });
});
