<?php

use App\Enums\DistributionMethod;
use App\Livewire\BillForm;
use App\Livewire\BillsManager;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('it displays "Les dépenses" as a title', function () {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Dépenses du foyer');
});


test('should display an empty table if no bills', function () {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Aucune dépense');
});

test('should displays existing bills in a table', function () {
    $household = Household::factory()->create();
    $member = Member::factory()->create([
        'household_id' => $household->id,
        'first_name' => 'Test',
        'last_name' => 'Member',
    ]);

    $bill = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => $member->id,
        'name' => 'Test dépense',
        'amount' => 1000,
        'distribution_method' => DistributionMethod::EQUAL
    ]);

    Livewire::test(BillsManager::class)
        ->assertSeeText('Test dépense')
        ->assertSee($bill->amount_formatted)
        ->assertSee('Test Member')
        ->assertSee($bill->distribution_method->label());
});

test('when a bill is not affected to a member, should display the bill without member', function () {
    $household = Household::factory()->create();
    $member = Member::factory()->create(['household_id' => $household->id]);
    $bill = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => null,
        'name' => 'Test dépense',
        'amount' => 1000,
        'distribution_method' => DistributionMethod::EQUAL
    ]);

    Livewire::test(BillsManager::class)
        ->assertSeeText('Test dépense')
        ->assertSeeText('Compte joint');
});

test('should display a clickable button to add a bill', function () {
    Livewire::test(BillsManager::class)
        ->assertSeeText('Ajouter');
});

test('should have form input to create a new bill', function () {
    Livewire::test(BillForm::class)
        ->assertSeeHtmlInOrder([
            'wire:model="newName"',
            'wire:model="newAmount"',
            'wire:model="newDistributionMethod"',
            'wire:model="newMemberId"'
        ]);
});

test('should offer distribution methods as options', function () {

    $distributionMethods = [
        'equal' => '50/50',
        'prorata' => 'Prorata'
    ];

    Livewire::test(BillForm::class,
        [
            'distributionMethods' => $distributionMethods,
        ])
        ->assertSeeHtmlInOrder(
            array_values($distributionMethods)
        );
});

test('should offer household members as options', function () {
    $memberHuey = Member::factory()->create([
        'first_name' => 'Huey',
        'last_name' => 'Duck',
    ]);
    $memberDewey = Member::factory()->create([
        'first_name' => 'Dewey',
        'last_name' => 'Duck',
        'household_id' => $memberHuey->household_id,
    ]);
    $memberLouis = Member::factory()->create([
        'first_name' => 'Louis',
        'last_name' => 'Duck',
        'household_id' => $memberHuey->household_id,
    ]);

    $householdMembers = [$memberHuey, $memberDewey, $memberLouis];

    Livewire::test(BillForm::class,
        [
            'householdMembers' => (new Collection($householdMembers))->mapWithKeys(
                function (Member $member) {
                    return [$member->id => $member->full_name];
                })->toArray()
        ])
        ->assertSeeHtmlInOrder(
            array_map(function (Member $member) {
                return $member->full_name;
            }, $householdMembers)
        );
});

test('should offer "compte joint" as an option', function () {
    Livewire::test(BillForm::class, [])
        ->assertSeeText('Compte joint');
});

