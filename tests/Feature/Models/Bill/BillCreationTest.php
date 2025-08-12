<?php

namespace Tests\Feature\Models;

use App\Exceptions\MismatchedHouseholdException;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use ValueError;

test('a bill can be created and associated with a household and a member', function () {
    list($household, $members) = bill_factory()->householdWithMembers(2);
    $bill = bill_factory()->bill([], $members->first());

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
    bill_factory()->bill(['name' => null]);
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the bill has no amount", function () {
    bill_factory()->bill(['amount' => null]);
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the bill has no distribution method", function () {
    bill_factory()->bill(['distribution_method' => null]);
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the distribution method is not valid", function () {
    bill_factory()->bill(['distribution_method' => 'invalid']);
})->throws(ValueError::class);

test("an exception is thrown if the bill has no household", function () {
    bill_factory()->bill(['household_id' => null]);;
})->throws(\InvalidArgumentException::class);

test("an exception is thrown if the bill has a member not associated with the household", function () {
    $householdA = bill_factory()->household();
    $householdB = bill_factory()->household();
    $member = Member::factory()->create([], $householdA);;

    Bill::factory()->create([
        'member_id' => $member->id,
        'household_id' => $householdB->id,
    ]);
})->throws(MismatchedHouseholdException::class);
