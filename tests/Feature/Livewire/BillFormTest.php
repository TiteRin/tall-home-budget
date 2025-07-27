<?php

use App\Livewire\BillForm;
use App\Models\Member;
use Illuminate\Support\Collection;
use Livewire\Livewire;

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

    Livewire::test(BillForm::class, [
        'hasJointAccount' => true
    ])->assertSeeText('Compte joint');
});

test('should not offer "compte joint" as as option if the household does not possess one’', function () {

    Livewire::test(BillForm::class, [
        'hasJointAccount' => false
    ])->assertDontSeeText('Compte joint');
});

