<?php

namespace Tests\Feature\Livewire\Household;

use App\Enums\DistributionMethod;
use App\Livewire\HouseholdManager;
use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('Initialization', function () {

    test("when household exists, should load existing data", function () {
        $household = Household::factory()->create([
            'name' => 'Famille Dupont',
            'has_joint_account' => true,
            'default_distribution_method' => DistributionMethod::PRORATA,
        ]);

        $member = Member::factory()->create([
            'household_id' => $household->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont'
        ]);

        $user = UserFactory::new()->create(['member_id' => $member->id]);
        $this->actingAs($user);

        Livewire::test(HouseholdManager::class)
            ->assertSet('householdId', $household->id)
            ->assertSet('householdName', 'Famille Dupont')
            ->assertSet('hasJointAccount', true)
            ->assertSet('defaultDistributionMethod', DistributionMethod::PRORATA->value)
            ->assertSet('newMemberLastName', 'Famille Dupont')
            ->assertCount('householdMembers', 1);
    });
});

describe('Save functionality', function () {
    beforeEach(function () {
        $this->household = Household::factory()->create([
            'name' => 'Ancien Nom',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $this->member = Member::factory()->create(['household_id' => $this->household->id]);
        $this->user = UserFactory::new()->create(['member_id' => $this->member->id]);
        $this->actingAs($this->user);
    });

    test("should update household with valid data", function () {
        Livewire::test(HouseholdManager::class)
            ->set('householdId', $this->household->id)
            ->set('householdName', 'Nouveau Nom')
            ->set('hasJointAccount', true)
            ->set('defaultDistributionMethod', DistributionMethod::PRORATA->value)
            ->call('save')
            ->assertSee('Foyer enregistré avec succès');

        $this->household->refresh();
        expect($this->household->name)->toBe('Nouveau Nom')
            ->and($this->household->has_joint_account)->toBeTrue()
            ->and($this->household->default_distribution_method)->toBe(DistributionMethod::PRORATA);
    });

    describe('validation', function () {
        test("should validate household name is required", function () {
            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->set('householdName', '') // Vide
                ->call('save')
                ->assertHasErrors(['householdName' => 'required']);
        });

        test("should validate household name minimum length", function () {
            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->set('householdName', 'A') // Trop court
                ->call('save')
                ->assertHasErrors(['householdName' => 'min']);
        });
    });
});

describe('Member management', function () {
    beforeEach(function () {
        $this->household = Household::factory()->create(['name' => 'Famille Test']);
        $this->member = Member::factory()->create(['household_id' => $this->household->id]);
        $this->user = UserFactory::new()->create(['member_id' => $this->member->id]);
        $this->actingAs($this->user);
    });

    test("should add member with valid data", function () {
        Livewire::test(HouseholdManager::class)
            ->set('newMemberFirstName', 'Marie')
            ->set('newMemberLastName', 'Dupont')
            ->call('addMember')
            ->assertSet('newMemberFirstName', '')
            ->assertSet('newMemberLastName', 'Famille Test')
            ->assertCount('householdMembers', 2);

        expect($this->household->members()->count())->toBe(2);
        $member = $this->household->members()->orderBy('id', 'desc')->first();
        expect($member->first_name)->toBe('Marie')
            ->and($member->last_name)->toBe('Dupont');
    });

    describe('add member validation', function () {
        test("should validate first name is required", function () {
            Livewire::test(HouseholdManager::class)
                ->set('newMemberFirstName', '')
                ->set('newMemberLastName', 'Dupont')
                ->call('addMember')
                ->assertHasErrors(['newMemberFirstName' => 'required']);
        });

        test("should validate first name minimum length", function () {
            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->set('newMemberFirstName', 'A')
                ->set('newMemberLastName', 'Dupont')
                ->call('addMember')
                ->assertHasErrors(['newMemberFirstName' => 'min']);
        });

        test("should validate last name is required", function () {
            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->set('newMemberFirstName', 'Marie')
                ->set('newMemberLastName', '')
                ->call('addMember')
                ->assertHasErrors(['newMemberLastName' => 'required']);
        });

        test("should validate last name minimum length", function () {
            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->set('newMemberFirstName', 'Marie')
                ->set('newMemberLastName', 'A')
                ->call('addMember')
                ->assertHasErrors(['newMemberLastName' => 'min']);
        });
    });

    describe('remove member', function () {
        test("should show confirmation modal instead of deleting directly", function () {
            $member = Member::factory()->create(['household_id' => $this->household->id, 'first_name' => 'Jean']);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 1) // Index 1 car index 0 est $this->member
                ->assertSet('memberIdToDelete', $member->id)
                ->assertDispatched('open-modal', 'confirm-delete-member');

            expect($this->household->members()->count())->toBe(2);
        });

        test("should delete member after confirmation", function () {
            $member = Member::factory()->create(['household_id' => $this->household->id]);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 1)
                ->call('performDelete')
                ->assertSet('memberIdToDelete', null)
                ->assertSee('Membre supprimé avec succès');

            expect($this->household->members()->count())->toBe(1);
        });

        test("should not allow deleting member with user account", function () {
            // $this->memberWithUser est à l'index 0 (créé dans le setup global si on était dans Member editing,
            // mais ici on est dans Member management setup)
            // Recréons une situation propre
            $memberWithUser = Member::factory()->create(['household_id' => $this->household->id]);
            User::factory()->create(['member_id' => $memberWithUser->id]);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 1)
                ->assertSet('memberIdToDelete', null);
        });

        test("should reassign bills to joint account by default", function () {
            $this->household->update(['has_joint_account' => true]);
            $member = Member::factory()->create(['household_id' => $this->household->id]);
            $bill = \App\Models\Bill::factory()->create([
                'household_id' => $this->household->id,
                'member_id' => $member->id,
                'name' => 'Loyer',
                'amount' => 1000,
                'distribution_method' => \App\Enums\DistributionMethod::EQUAL
            ]);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 1)
                ->assertSet('impactedBillsCount', 1)
                ->assertSet('reassignmentTarget', 'compte joint')
                ->call('performDelete');

            $bill->refresh();
            expect($bill->member_id)->toBeNull()
                ->and(Member::find($member->id))->toBeNull();
        });

        test("should reassign bills to first other member if no joint account", function () {
            $this->household->update(['has_joint_account' => false]);
            // $this->member est le premier membre (index 0)
            $memberToDelete = Member::factory()->create(['household_id' => $this->household->id]);
            $bill = \App\Models\Bill::factory()->create([
                'household_id' => $this->household->id,
                'member_id' => $memberToDelete->id,
                'name' => 'Courses',
                'amount' => 100,
                'distribution_method' => \App\Enums\DistributionMethod::EQUAL
            ]);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 1)
                ->assertSet('reassignmentTarget', $this->member->full_name)
                ->call('performDelete');

            $bill->refresh();
            expect($bill->member_id)->toBe($this->member->id);
        });

        test("should delete bills if requested", function () {
            $member = Member::factory()->create(['household_id' => $this->household->id]);
            $bill = \App\Models\Bill::factory()->create([
                'household_id' => $this->household->id,
                'member_id' => $member->id,
                'name' => 'Netflix',
                'amount' => 15,
                'distribution_method' => \App\Enums\DistributionMethod::EQUAL
            ]);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 1)
                ->set('deleteAction', 'delete_bills')
                ->call('performDelete');

            expect(\App\Models\Bill::find($bill->id))->toBeNull()
                ->and(Member::find($member->id))->toBeNull();
        });

        test("should handle invalid index gracefully", function () {
            $member = Member::factory()->create(['household_id' => $this->household->id]);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 999) // Index invalide
                ->assertCount('householdMembers', 2);

            expect($this->household->members()->count())->toBe(2);
        });

        test("should handle empty members array", function () {
            // Ne pas supprimer $this->member car l'utilisateur connecté en dépend
            $this->household->members()->where('id', '!=', $this->member->id)->delete();

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('refreshMembers')
                ->assertCount('householdMembers', 1); // Reste $this->member
        });
    });
});

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

describe('Utility methods', function () {

    beforeEach(function() {
        $this->household = Household::factory()->create();
        $this->member = Member::factory()->create(['household_id' => $this->household->id]);
        $this->user = UserFactory::new()->create(['member_id' => $this->member->id]);
        $this->actingAs($this->user);
    });

    test("updatedHouseholdName() should update newMemberLastName", function () {
        Livewire::test(HouseholdManager::class)
            ->set('householdName', 'Famille Martin')
            ->assertSet('newMemberLastName', 'Famille Martin');
    });

    test("getHouseholdProperty() should return correct household", function () {

        $component = Livewire::test(HouseholdManager::class)
            ->set('householdId', $this->household->id);

        expect($component->instance()->household->id)->toBe($this->household->id);
    });

    test("getHouseholdProperty() should return null for invalid id", function () {
        $component = Livewire::test(HouseholdManager::class)
            ->set('householdId', 999);

        expect($component->instance()->household)->toBeNull();
    });

    test("getDistributionMethodOptionsProperty() should return correct options", function () {
        $component = Livewire::test(HouseholdManager::class);

        $options = $component->instance()->distributionMethodOptions;
        expect($options)->toBeArray()
            ->and($options)->toHaveKey(DistributionMethod::EQUAL->value)
            ->and($options)->toHaveKey(DistributionMethod::PRORATA->value);
    });

    test("refreshMembers() should update householdMembers array", function () {
        $component = Livewire::test(HouseholdManager::class)
            ->set('householdId', $this->household->id)
            ->assertCount('householdMembers', 1);

        // Ajouter un membre directement en base
        Member::factory()->create(['household_id' => $this->household->id]);

        $component->call('refreshMembers')
            ->assertCount('householdMembers', 2);
    });

    test("getInviteLink() should return a signed URL", function () {
        $member = Member::factory()->create();

        $component = Livewire::test(HouseholdManager::class);

        $url = $component->instance()->getInviteLink($member->id);

        expect($url)->toContain('register')
            ->and($url)->toContain('member_id=' . $member->id)
            ->and($url)->toContain('signature=');
    });
});
