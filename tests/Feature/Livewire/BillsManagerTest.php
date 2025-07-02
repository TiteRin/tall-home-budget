<?php

use App\Enums\DistributionMethod;
use App\Livewire\BillsManager;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it displays "Les dépenses" as a title', function() {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Dépenses du foyer');
});


test('should display an empty table if no bills', function() {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Aucune dépense');
});

test('should displays existing bills in a table', function()
{
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

test('when a bill is not affected to a member, should display the bill without member', function()
{
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

test('should display a clickable button to add a bill', function()
{
    Livewire::test(BillsManager::class)
        ->assertSeeText('Ajouter');
});

test('should have form input to create a new bill', function()
{
    Livewire::test(BillsManager::class)
        ->assertSeeHtmlInOrder([
            'wire:model="newName"',
            'wire:model="newAmount"',
            'wire:model="newDistributionMethod"',
            'wire:model="newMemberId"'
        ]);
});

test('should offer distribution methods as options', function()
{
   Livewire::test(BillsManager::class)
        ->assertSeeHtmlInOrder(
            collect(DistributionMethod::cases())->map(function(DistributionMethod $distributionMethod)
            {
                return $distributionMethod->label();
            })
        );
});


