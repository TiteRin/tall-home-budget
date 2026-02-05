<?php

use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use App\Services\Household\CurrentHouseholdService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

describe('CurrentHouseholdService', function () {
    beforeEach(function () {
        $this->service = new CurrentHouseholdService();
    });

    test('get current household returns null if not authenticated', function () {
        Auth::logout();
        expect($this->service->getCurrentHousehold())->toBeNull();
    });

    test('get current household returns household for authenticated user', function () {
        $household = Household::factory()->create();
        $member = Member::factory()->create(['household_id' => $household->id]);
        $user = User::factory()->create(['member_id' => $member->id]);

        $this->actingAs($user);

        $result = $this->service->getCurrentHousehold();

        expect($result)->not->toBeNull()
            ->and($result->id)->toBe($household->id);
    });

    test('get household finds by id', function () {
        $household = Household::factory()->create();

        $result = $this->service->getHousehold($household->id);

        expect($result)->not->toBeNull()
            ->and($result->id)->toBe($household->id);
    });
});
