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
    test("when no household exists, should initialize one", function () {
        Livewire::test(HouseholdManager::class)
            ->assertSet('householdName', 'Mon Foyer')
            ->assertSet('hasJointAccount', false)
            ->assertSet('defaultDistributionMethod', DistributionMethod::EQUAL->value)
            ->assertSet('newMemberLastName', 'Mon Foyer')
            ->assertCount('householdMembers', 0);

        // Vérifier qu'un foyer a bien été créé
        expect(Household::count())->toBe(1);
        $household = Household::first();
        expect($household->name)->toBe('Mon Foyer')
            ->and($household->has_joint_account)->toBeFalse()
            ->and($household->default_distribution_method)->toBe(DistributionMethod::EQUAL);
    });

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
    });

    test("should add member with valid data", function () {
        Livewire::test(HouseholdManager::class)
            ->set('householdId', $this->household->id)
            ->set('newMemberFirstName', 'Marie')
            ->set('newMemberLastName', 'Dupont')
            ->call('addMember')
            ->assertSet('newMemberFirstName', '')
            ->assertSet('newMemberLastName', 'Famille Test')
            ->assertCount('householdMembers', 1);

        expect($this->household->members()->count())->toBe(1);
        $member = $this->household->members()->first();
        expect($member->first_name)->toBe('Marie')
            ->and($member->last_name)->toBe('Dupont');
    });

    describe('add member validation', function () {
        test("should validate first name is required", function () {
            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
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
        test("should remove member by index", function () {
            $member1 = Member::factory()->create(['household_id' => $this->household->id, 'first_name' => 'Jean']);
            $member2 = Member::factory()->create(['household_id' => $this->household->id, 'first_name' => 'Marie']);
            $user = User::factory()->create(['member_id' => $member1->id]);
            $this->actingAs($user);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 0)
                ->assertCount('householdMembers', 1);

            expect($this->household->members()->count())->toBe(1);
            expect($this->household->members()->first()->first_name)->toBe('Marie');
        });

        test("should handle invalid index gracefully", function () {
            $member = Member::factory()->create(['household_id' => $this->household->id]);

            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 999) // Index invalide
                ->assertCount('householdMembers', 1);

            expect($this->household->members()->count())->toBe(1);
        });

        test("should handle empty members array", function () {
            Livewire::test(HouseholdManager::class)
                ->set('householdId', $this->household->id)
                ->call('removeMember', 0)
                ->assertCount('householdMembers', 0);

            expect($this->household->members()->count())->toBe(0);
        });
    });
});

describe('Utility methods', function () {
    test("updatedHouseholdName() should update newMemberLastName", function () {
        Livewire::test(HouseholdManager::class)
            ->set('householdName', 'Famille Martin')
            ->assertSet('newMemberLastName', 'Famille Martin');
    });

    test("getHouseholdProperty() should return correct household", function () {
        $household = Household::factory()->create();

        $component = Livewire::test(HouseholdManager::class)
            ->set('householdId', $household->id);

        expect($component->instance()->household->id)->toBe($household->id);
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
        $household = Household::factory()->create();
        $member = Member::factory()->create(['household_id' => $household->id]);
        $user = User::factory()->create(['member_id' => $member->id]);

        $this->actingAs($user);

        $component = Livewire::test(HouseholdManager::class)
            ->set('householdId', $household->id)
            ->assertCount('householdMembers', 1);

        // Ajouter un membre directement en base
        Member::factory()->create(['household_id' => $household->id]);

        $component->call('refreshMembers')
            ->assertCount('householdMembers', 2);
    });
});
