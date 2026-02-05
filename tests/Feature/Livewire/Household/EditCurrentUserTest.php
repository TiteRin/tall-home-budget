<?php

namespace Tests\Feature\Livewire\Household;

use App\Livewire\HouseholdManager;
use App\Models\Household;
use App\Models\Member;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test("le membre connecté peut modifier ses propres informations", function () {
    $household = Household::factory()->create();
    $member = Member::factory()->create([
        'household_id' => $household->id,
        'first_name' => 'Jean',
        'last_name' => 'Dupont'
    ]);
    $user = UserFactory::new()->create(['member_id' => $member->id]);
    $this->actingAs($user);

    Livewire::test(HouseholdManager::class)
        ->call('editMember', 0)
        ->assertSet('editingMemberId', $member->id)
        ->set('editingMemberFirstName', 'Jacques')
        ->set('editingMemberLastName', 'Martin')
        ->call('updateMember')
        ->assertSet('editingMemberId', null);

    $member->refresh();
    expect($member->first_name)->toBe('Jacques')
        ->and($member->last_name)->toBe('Martin');
});

test("le membre connecté ne peut pas modifier les informations d'un autre utilisateur", function () {
    $household = Household::factory()->create();

    // Moi
    $me = Member::factory()->create(['household_id' => $household->id]);
    $myUser = UserFactory::new()->create(['member_id' => $me->id]);

    // Un autre utilisateur
    $otherMember = Member::factory()->create(['household_id' => $household->id]);
    $otherUser = UserFactory::new()->create(['member_id' => $otherMember->id]);

    $this->actingAs($myUser);

    Livewire::test(HouseholdManager::class)
        ->call('editMember', $otherMember->id) // Index de l'autre membre
        ->assertSet('editingMemberId', null);
});
