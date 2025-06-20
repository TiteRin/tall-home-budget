<?php

use App\DistributionMethod;
use App\Models\Bill;
use App\Models\Household;
use App\Models\HouseholdMember;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a bill can be created with a name, amount, a distribution method and a household', function () {
    
    $bill = Bill::factory()->create();

    expect($bill)->toBeInstanceOf(Bill::class);
    expect($bill->household)->toBeInstanceOf(Household::class);
    expect($bill->householdMember)->toBeInstanceOf(HouseholdMember::class);

    $this->assertDatabaseHas('bills', [
        'id' => $bill->id,
        'name' => $bill->name
    ]);
});