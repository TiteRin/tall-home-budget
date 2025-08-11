<?php

namespace Tests\Feature\Models;

use App\Exceptions\MismatchedHouseholdException;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use ValueError;

test('a bill can be created and associated with a household and a member', function () {
    $household = Household::factory()->create();
    $member = Member::factory()->create(['household_id' => $household->id]);
    $bill = Bill::factory()->create(['household_id' => $household->id, 'member_id' => $member->id]);

    // Assert: Vérifiez que les relations sont correctes et que les données existent.
    expect($bill)->toBeInstanceOf(Bill::class)
        ->and($bill->id)->toBeInt()
        ->and($bill->household)->toBeInstanceOf(Household::class)
        ->and($bill->member)->toBeInstanceOf(Member::class);

    // On peut aussi directement vérifier la présence en base de données
    $this->assertDatabaseHas('bills', [
        'id' => $bill->id,
        'name' => $bill->name
    ]);
});

test('an exception is thrown when saving an empty bill', function()
{
    $bill = new Bill();
    $bill->save();
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the bill has no name", function () {
    $bill = Bill::factory()->create([
        'name' => null,
    ]);
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the bill has no amount", function () {
    $bill = Bill::factory()->create([
        'amount' => null,
    ]);
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the bill has no distribution method", function () {
    $bill = Bill::factory()->create([
        'distribution_method' => null,
    ]);
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the distribution method is not valid", function () {
    $bill = Bill::factory()->create([
        'distribution_method' => "invalid",
    ]);
})->throws(ValueError::class);

test("an exception is thrown if the bill has no household", function () {
    $bill = Bill::factory()->create([
        'household_id' => null,
    ]);
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the bill has a member not associated with the household", function () {
    $member = Member::factory()->create();
    $houseHoldB = Household::factory()->create([]);

    Bill::factory()->create([
        'member_id' => $member->id,
        'household_id' => $houseHoldB->id,
    ]);
})->throws(MismatchedHouseholdException::class);
