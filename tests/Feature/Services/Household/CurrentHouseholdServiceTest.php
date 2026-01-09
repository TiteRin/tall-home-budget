<?php

namespace Tests\Feature\Services\Household;

use App\Models\Household;
use App\Models\User;
use App\Services\Household\CurrentHouseholdService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('HouseholdService', function () {
    beforeEach(function () {
        $this->service = new CurrentHouseholdService();
    });

    describe('getCurrentHousehold()', function () {
        test('should return null when no households exist', function () {
            $result = $this->service->getCurrentHousehold();

            expect($result)->toBeNull();
        });

        test('should return null when no one is authentified', function () {
            if (auth()->check()) auth()->logout();

            expect($this->service->getCurrentHousehold())->toBeNull();
        });

        test('should return household when authentified', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            expect($this->service->getCurrentHousehold())->toBeInstanceOf(Household::class);
            expect($this->service->getCurrentHousehold()->id)->toBe($user->member->household_id);
        });
    });
});
