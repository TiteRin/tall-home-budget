<?php

use App\Actions\Bills\CreateBill;
use App\Enums\DistributionMethod;

describe("BillsController", function () {

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

    it('should return a 201 response when valid data are sent to the store API', function () {
        $response = $this->postJson(route('bills.store'), $this->payload);

        $response->assertCreated();
        $response->assertJsonFragment(['message' => 'Bill created successfully']);
    });

    it('should return a 200 response for index', function () {
        $response = $this->get(route('bills'));
        $response->assertOk();
    });

    it('should return a 422 response for invalid data', function () {
        $response = $this->postJson(route('bills.store'), []);
        $response->assertStatus(422);
    });

    it('should return a 422 response when handle fails', function () {
        $this->mock(CreateBill::class, function ($mock) {
            $mock->shouldReceive('handle')->andThrow(new \Exception('Mocked error'));
        });

        $response = $this->postJson(route('bills.store'), $this->payload);
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'An error occurred while creating the bill']);
    });
});

