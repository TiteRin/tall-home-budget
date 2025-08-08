<?php

namespace Tests\Feature;

use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use function Pest\Laravel\get;

const CURRENCY = "EUR";
const TITLE = "Les dépenses du foyer";
const NO_BILLS_MESSAGE = "Aucune dépense";
const DEFAULT_BILL_NAME = "Electricity";
const DEFAULT_BILL_AMOUNT = 17900;
const DEFAULT_BILL_AMOUNT_FORMATTED = "179,00 €";
const DEFAULT_BILL_DISTRIBUTION_METHOD = DistributionMethod::PRORATA;

function createDefaultHousehold(): Household
{
    return Household::factory()->create();
}

function createDefaultMember(array $overrides = [], Household $household = null): Member
{
    $household = $household ?? createDefaultHousehold();
    return Member::factory()->create([
        'household_id' => $household->id,
        ...$overrides,
    ]);
}

function createDefaultBill(array $overrides = [], Member $member = null): Bill
{
    $member = $member ?? createDefaultMember();
    return Bill::factory()->create([
        'member_id' => $member->id,
        'household_id' => $member->household_id,
        'name' => DEFAULT_BILL_NAME,
        'amount' => DEFAULT_BILL_AMOUNT,
        'distribution_method' => DEFAULT_BILL_DISTRIBUTION_METHOD,
        ...$overrides,
    ]);
}

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


    $bill = createDefaultBill();

    $response = get("/bills");

    $response->assertSeeText($bill->name);
});

test('user shouldn’t see other household’s bills', function() {

    $defaultHousehold = createDefaultHousehold();
    $anotherHousehold = Household::factory()->create();

    $bill1 = $defaultHousehold->bills()->create([
        'name' => 'Test Bill 1',
        'amount' => 10000,
        'member_id' => Member::factory()->create(['first_name' => 'John', 'last_name' => 'Doe', 'household_id' => $defaultHousehold->id])->id,
        'distribution_method' => DistributionMethod::EQUAL,
    ]);
    $bill2 = $anotherHousehold->bills()->create([
        'name' => 'Test Bill 2',
        'amount' => 10000,
        'member_id' => Member::factory()->create(['first_name' => 'Dewey', 'last_name' => 'Duck', 'household_id' => $anotherHousehold->id])->id,
        'distribution_method' => DistributionMethod::EQUAL,
    ]);

    $response = get("/bills");

    $response->assertSeeText('Test Bill 1');
    $response->assertDontSeeText('Test Bill 2');
});

test('amount should be formatted as a currency', function() {
    $bill = createDefaultBill(['amount' => 179000]);

    $response = get("/bills");

    $response->assertSeeText('1 790,00 €');
});

test('user should be able to edit a bill', function() {
    $bill = createDefaultBill();

    $response = get("/bills");

    $response->assertSeeText("Modifier");
});

test('user should be able to delete a bill', function() {
    $bill = createDefaultBill();

    $response = get("/bills");

    $response->assertSeeText("Supprimer");
});

test('user should see a button to add a bill', function() {
    $response = get("/bills");

    $response->assertSeeText("Ajouter une dépense");
});

test('user should see the total amount of bills', function() {
    $bill1 = createDefaultBill(['amount' => 10000]);
    $bill2 = createDefaultBill(['amount' => 20000], $bill1->member);
    $bill3 = createDefaultBill(['amount' => 7000], $bill1->member);
    $anotherMemberBill = createDefaultBill(['amount' => 10000]);

    $response = get("/bills");

    $response->assertSeeText('370,00 €');
});
