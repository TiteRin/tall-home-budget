<?php

namespace Tests\Feature\Models;

use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use App\Exceptions\MismatchedHouseholdException;
use ValueError;

uses(RefreshDatabase::class);

test('a bill can be created and associated with a household and a member', function () {
    // Arrange: Créez une facture et toutes ses dépendances en une seule ligne !
    $bill = Bill::factory()->create();

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

test("an exception is thrown if the bill has no name", function () {
    $bill = Bill::factory()->create([
        'name' => null,
    ]);
})->throws(QueryException::class);

test("an exception is thrown if the bill has no amount", function () {
    $bill = Bill::factory()->create([
        'amount' => null,
    ]);
})->throws(QueryException::class);

test("an exception is thrown if the bill has no distribution method", function () {
    $bill = Bill::factory()->create([
        'distribution_method' => null,
    ]);
})->throws(QueryException::class);

test("an exception is thrown if the distribution method is not valid", function () {
    $bill = Bill::factory()->create([
        'distribution_method' => "invalid",
    ]);
})->throws(ValueError::class);

test("an exception is thrown if the bill has no household", function () {
    $bill = Bill::factory()->create([
        'household_id' => null,
    ]);
})->throws(QueryException::class);

test("an exception is thrown if the bill has a member not associated with the household", function () {
    $bill = Bill::factory()->create([
        'member_id' => Member::factory(),
    ]);
})->throws(MismatchedHouseholdException::class);