<?php

namespace Tests\Http\Controllers;

use App\Enums\DistributionMethod;

describe("when valid data are sent to the store API", function () {

    beforeEach(function () {

        $context = test_factory()
            ->withHousehold()
            ->withMember()
            ->withUser();

        $this->member = $context->member();
        $this->household = $context->household();
        $this->user = $context->user();

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

