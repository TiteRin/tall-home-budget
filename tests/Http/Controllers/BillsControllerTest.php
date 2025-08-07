<?php

namespace Tests\Http\Controllers;

use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe("when valid data are sent to the store API", function () {

    beforeEach(function () {

        $this->household = Household::factory()->create();
        $this->member = Member::factory()->create(['household_id' => $this->household->id]);

        $this->payload = [
            'name' => 'Test bill',
            'household_id' => $this->household->id,
            'member_id' => $this->member->id,
            'amount' => 10000,
            'distribution_method' => DistributionMethod::EQUAL
        ];
    });

    it('should return a 201 response', function () {
        $response = $this->postJson(route('bills.store'), $this->payload);

        $response->assertCreated();
        $response->assertJsonFragment(['message' => 'Bill created successfully']);
    });

    it('should save a new bill', function () {

    });

    it('should emit a notification', function () {

    });
});

