<?php

use App\Enums\DistributionMethod;
use App\Livewire\BillsManager;
use Livewire\Livewire;

beforeEach(function () {
    $this->household = bill_factory()->household();
});

test('it displays "Les dépenses" as a title', function () {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Dépenses du foyer');
});


test('should display an empty table if no bills', function () {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Aucune dépense');
});

test('should display existing bills in a table', function () {

    $member = bill_factory()->member([
        'first_name' => 'Test',
        'last_name' => 'Member',
    ], $this->household);;

    $bill = bill_factory()->bill([
        'name' => 'Test dépense',
        'amount' => 1000,
        'distribution_method' => DistributionMethod::EQUAL
    ], $member, $this->household);

    Livewire::test(BillsManager::class)
        ->assertSeeText('Test dépense')
        ->assertSee('10,00 €')
        ->assertSee('Test Member')
        ->assertSee($bill->distribution_method->label());
});

test('when a bill is not affected to a member, should display the bill without member', function () {
    $member = bill_factory()->member([], $this->household);
    $bill = bill_factory()->bill([
        'name' => 'Test dépense',
        'amount' => 1000,
        'distribution_method' => DistributionMethod::EQUAL,
        'member_id' => null,
    ], null, $this->household);;

    Livewire::test(BillsManager::class)
        ->assertSeeText('Test dépense')
        ->assertSeeText('Compte joint');
});

test('should use the BillForm component to add a bill', function () {
    Livewire::test(BillsManager::class)
        ->assertSeeLivewire('bill-form');
});
