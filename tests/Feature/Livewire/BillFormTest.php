<?php

use App\Enums\DistributionMethod;
use App\Livewire\BillForm;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('should have form input to create a new bill', function () {

    Livewire::test(BillForm::class)
        ->assertSeeHtmlInOrder([
            'wire:model="newName"',
            'wire:model.blur="formattedNewAmount"',
            'wire:model="newDistributionMethod"',
            'wire:model="newMemberId"'
        ]);
});

test('should offer distribution methods as options', function () {

    $distributionMethods = DistributionMethod::options();

    Livewire::test(BillForm::class)
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
            'householdMembers' => collect($householdMembers)
        ])
        ->assertSeeHtmlInOrder(
            array_map(function (Member $member) {
                return $member->full_name;
            }, $householdMembers)
        );
});

test('should display placeholder if no amount given', function() {
    Livewire::test(BillForm::class)
        ->assertSee('Montant');
});

test('should display formatted value if amount given', function() {
   Livewire::test(BillForm::class)
       ->set('formattedNewAmount', '1000')
       ->assertSet('newAmount', 100000)
       ->assertSet('formattedNewAmount', '1 000,00 €')
       ->assertSeeHtml('value="1 000,00 €"');
});

test('should offer "compte joint" as an option by default', function () {

    Livewire::test(BillForm::class)->assertSeeText('Compte joint');
});

test('should not offer "compte joint" as as option otherwise', function () {

    Livewire::test(BillForm::class, [
        'hasJointAccount' => false
    ])->assertDontSeeText('Compte joint');
});

test('should offer the preferred distribution method by default', function () {
    Livewire::test(BillForm::class, ['defaultDistributionMethod' => DistributionMethod::PRORATA])
        ->assertSet('newDistributionMethod', DistributionMethod::PRORATA->value);
});
