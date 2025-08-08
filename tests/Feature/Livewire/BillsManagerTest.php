<?php

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Livewire\BillsManager;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use App\Services\HouseholdService;
use Livewire\Livewire;

beforeEach(function () {
    Household::factory()->create();
    $this->householdService = new HouseholdService();
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
    $household = $this->householdService->getCurrentHousehold();
    $member = Member::factory()->create([
        'household_id' => $household->id,
        'first_name' => 'Test',
        'last_name' => 'Member',
    ]);

    $amount = new Amount(1000);

    $bill = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => $member->id,
        'name' => 'Test dépense',
        'amount' => $amount,
        'distribution_method' => DistributionMethod::EQUAL
    ]);

    Livewire::test(BillsManager::class)
        ->assertSeeText('Test dépense')
        ->assertSee($amount->toCurrency())
        ->assertSee('Test Member')
        ->assertSee($bill->distribution_method->label());
});

test('when a bill is not affected to a member, should display the bill without member', function () {
    $household = $this->householdService->getCurrentHousehold();
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

test('should use the BillForm component to add a bill', function () {
    Livewire::test(BillsManager::class)
        ->assertSeeLivewire('bill-form');
});
