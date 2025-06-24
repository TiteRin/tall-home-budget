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