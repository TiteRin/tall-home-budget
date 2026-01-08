<?php

namespace Tests\Feature\Http\Controllers\Home;

use App\Enums\DistributionMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('should redirect to household settings when no household exists', function () {
    $user = \App\Models\User::factory()->create();
    $response = $this->actingAs($user)->get(route('home'));
    $response->assertRedirect(route('household.settings'));
});

test('should show home page when household exists', function () {
    $household = bill_factory()->household([
        'name' => 'Test Household',
        'has_joint_account' => false,
        'default_distribution_method' => DistributionMethod::EQUAL,
    ]);

    $member = \App\Models\Member::factory()->create([
        'household_id' => $household->id,
    ]);

    $user = \App\Models\User::factory()->create([
        'member_id' => $member->id,
    ]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertSuccessful();
    $response->assertViewIs('home');
    $response->assertViewHas('household', $household);
    $response->assertSee('Foyer Test Household');
    $response->assertSee('50/50'); // Label pour EQUAL
    $response->assertSee('Non'); // Compte joint
});
