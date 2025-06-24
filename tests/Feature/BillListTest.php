<?php

namespace App\Tests\Feature;

use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use App\Enums\DistributionMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('user can view bills list page', function() {
    $response = get("/bills");

    $response->assertStatus(200);
    $response->assertSeeText("Les dépenses du foyer");
});

test('user should see "Aucune dépense" if there are no bills', function() {
    $response = get("/bills");

    $response->assertStatus(200);
    $response->assertSeeText("Aucune dépense");
});

test('user should see the bills list if there are bills', function() {


    $household = Household::factory()->create();
    $member = Member::factory()->create([
        'household_id' => $household->id,
    ]);
    $bill = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => $member->id,
        'name' => "Electricity",
        'amount' => 17900,
        'distribution_method' => DistributionMethod::PRORATA,
    ]);

    $response = get("/bills");

    $response->assertSeeText("Electricity");
});

test('user should see its household’s bills list', function() {
    $household = Household::factory()->create();
    $member = Member::factory()->create([
        'household_id' => $household->id,
    ]);
    $bill = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => $member->id,
        'name' => "Electricity",   
        'amount' => 17900,
        'distribution_method' => DistributionMethod::PRORATA,
    ]);

    $response = get("/bills");

    $response->assertSeeText("Electricity");
});

test('user shouldn’t see other household’s bills', function() {
    $household = Household::factory()->create();
    $household2 = Household::factory()->create();
    $member1 = Member::factory()->create([
        'household_id' => $household->id,
    ]);
    $member2 = Member::factory()->create([
        'household_id' => $household2->id,
    ]);
    $bill1 = Bill::factory()->create([
        'household_id' => $household->id,
        'member_id' => $member1->id,
        'name' => "Electricity",
        'amount' => 17900,
        'distribution_method' => DistributionMethod::PRORATA,
    ]);
    $bill2 = Bill::factory()->create([
        'household_id' => $household2->id,
        'member_id' => $member2->id,
        'name' => "Gas",
        'amount' => 10000,
        'distribution_method' => DistributionMethod::PRORATA,
    ]);

    $response = get("/bills");

    $response->assertSeeText("Electricity");
    $response->assertDontSeeText("Gas");
});