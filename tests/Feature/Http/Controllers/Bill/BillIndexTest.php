<?php

namespace Tests\Feature;

use App\Enums\DistributionMethod;
use function Pest\Laravel\get;

const CURRENCY = "EUR";
const TITLE = "Les dépenses du foyer";
const NO_BILLS_MESSAGE = "Aucune dépense";
const DEFAULT_BILL_NAME = "Electricity";
const DEFAULT_BILL_AMOUNT = 17900;
const DEFAULT_BILL_AMOUNT_FORMATTED = "179,00 €";
const DEFAULT_BILL_DISTRIBUTION_METHOD = DistributionMethod::PRORATA;

test('user can view bills list page', function() {
    $response = get("/bills");

    $response->assertStatus(200);
    $response->assertSeeText(TITLE);
});

test('user should see "Aucune dépense" if there are no bills', function() {
    $response = get("/bills");

    $response->assertStatus(200);
    $response->assertSeeText(NO_BILLS_MESSAGE);
});

test('user should see the bills list if there are bills', function() {

    $bill = bill_factory()->bill(['name' => 'Test Bill']);

    $response = get("/bills");

    $response->assertSeeText($bill->name);
});

test('user shouldn’t see other household’s bills', function() {

    $defaultHousehold = bill_factory()->household();
    $anotherHousehold = bill_factory()->household();

    $bill1 = $defaultHousehold->bills()->create([
        'name' => 'Test Bill 1',
        'amount' => 10000,
        'member_id' => bill_factory()->member(['first_name' => 'Huey', 'last_name' => 'Duck'], $defaultHousehold)->id,
        'distribution_method' => DistributionMethod::EQUAL,
    ]);
    $bill2 = $anotherHousehold->bills()->create([
        'name' => 'Test Bill 2',
        'amount' => 10000,
        'member_id' => bill_factory()->member(['first_name' => 'Dewey', 'last_name' => 'Duck'], $anotherHousehold)->id,
        'distribution_method' => DistributionMethod::EQUAL,
    ]);

    $response = get("/bills");

    $response->assertSeeText('Test Bill 1');
    $response->assertDontSeeText('Test Bill 2');
});

test('amount should be formatted as a currency', function() {
    bill_factory()->bill(['amount' => 179000]);
    $response = get("/bills");
    $response->assertSeeText('1 790,00 €');
});

test('user should be able to edit a bill', function() {
    bill_factory()->bill();
    $response = get("/bills");
    $response->assertSeeText("Modifier");
});

test('user should be able to delete a bill', function() {
    bill_factory()->bill();
    $response = get("/bills");
    $response->assertSeeText("Supprimer");
});

test('user should see a button to add a bill', function() {
    $response = get("/bills");
    $response->assertSeeText("Ajouter une dépense");
});

test('user should see the total amount of bills', function() {
    $bill1 = bill_factory()->bill(['amount' => 10000]);
    $bill2 = bill_factory()->bill(['amount' => 20000], $bill1->member);
    $bill3 = bill_factory()->bill(['amount' => 7000], $bill1->member);
    $anotherMemberBill = bill_factory()->bill(['amount' => 10000]);

    $response = get("/bills");

    $response->assertSeeText('370,00 €');
});
