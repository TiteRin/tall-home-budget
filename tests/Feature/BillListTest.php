<?php

namespace App\Tests\Feature;

use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use App\Enums\DistributionMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

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


    $household = Household::factory()->create();
    $member = Member::factory()->create([
        'household_id' => $household->id,
    ]);
    $bill = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => $member->id,
        'name' => DEFAULT_BILL_NAME,
        'amount' => DEFAULT_BILL_AMOUNT,
        'distribution_method' => DEFAULT_BILL_DISTRIBUTION_METHOD,
    ]);

    $response = get("/bills");

    $response->assertSeeText(DEFAULT_BILL_NAME);
});

test('user should see its household’s bills list', function() {
    $household = Household::factory()->create();
    $member = Member::factory()->create([
        'household_id' => $household->id,
    ]);
    $bill = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => $member->id,
        'name' => DEFAULT_BILL_NAME,   
        'amount' => DEFAULT_BILL_AMOUNT,
        'distribution_method' => DEFAULT_BILL_DISTRIBUTION_METHOD,
    ]);

    $response = get("/bills");

    $response->assertSeeText(DEFAULT_BILL_NAME);
});

test('user shouldn’t see other household’s bills', function() {
    $household1 = Household::factory()->create();
    $household2 = Household::factory()->create();
    $member1 = Member::factory()->create([
        'household_id' => $household1->id,
    ]);
    $member2 = Member::factory()->create([
        'household_id' => $household2->id,
    ]);
    $bill1 = Bill::factory()->create([
        'household_id' => $household1->id,
        'member_id' => $member1->id,
        'name' => DEFAULT_BILL_NAME,
        'amount' => DEFAULT_BILL_AMOUNT,
        'distribution_method' => DEFAULT_BILL_DISTRIBUTION_METHOD,
    ]);
    $bill2 = Bill::factory()->create([
        'household_id' => $household2->id,
        'member_id' => $member2->id,
        'name' => "Phone Bill",
        'amount' => 10000,
        'distribution_method' => DistributionMethod::PRORATA,
    ]);

    $response = get("/bills");

    $response->assertSeeText(DEFAULT_BILL_NAME);
    $response->assertDontSeeText("Phone Bill");
});

test('amount should be formatted as a currency', function() {
    $household = Household::factory()->create();
    $member = Member::factory()->create([
        'household_id' => $household->id,
    ]);
    $bill = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => $member->id,
        'name' => DEFAULT_BILL_NAME,
        'amount' => DEFAULT_BILL_AMOUNT,
        'distribution_method' => DEFAULT_BILL_DISTRIBUTION_METHOD,
    ]);

    $response = get("/bills");

    $response->assertSeeText(DEFAULT_BILL_AMOUNT_FORMATTED);
});

test('user should see a button to add a bill', function() {
    $response = get("/bills");

    $response->assertSeeText("Ajouter une dépense");
});