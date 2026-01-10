<?php

namespace Tests\Http\Controllers;

use App\Enums\DistributionMethod;
use App\Models\User;

describe("when valid data are sent to the store API", function () {

    beforeEach(function () {

        $this->member = bill_factory()->member();
        $this->household = $this->member->household;
        $this->user = User::factory()->create(['member_id' => $this->member->id]);
        $this->actingAs($this->user);

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

//    it('should save a new bill', function () {
//
//    });
//
//    it('should emit a notification', function () {
//
//    });
});

