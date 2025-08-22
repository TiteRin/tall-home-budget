<?php

namespace Tests\Feature\Http\Controllers\Bills;

use App\Enums\DistributionMethod;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;

uses(RefreshDatabase::class);


describe("Golden Master", function () {
    beforeEach(function () {
        $this->member = bill_factory()->member();
        $this->household = $this->member->household;

        $this->payload = [
            'name' => 'Test bill',
            'household_id' => $this->household->id,
            'member_id' => $this->member->id,
            // L’API attend un "amount" numérique (centimes)
            'amount' => 10000,
            'distribution_method' => DistributionMethod::EQUAL->value, // "equal"
        ];
    });


    test('store should return 201 and success message with a bill object in payload', function () {
        // Act
        $response = $this->postJson(route('bills.store'), $this->payload);

        // Assert minimal (stabilise l’API tout en laissant de la marge pour la refacto interne)
        $response->assertCreated();
        $response->assertJsonFragment(['message' => 'Bill created successfully']);

        // On ne fige pas toutes les valeurs, seulement la structure
        $response->assertJsonStructure([
            'message',
            'bill' => [
                'id',
                'name',
                'amount',
                'distribution_method',
                'household_id',
                'member_id',
            ],
        ]);
    });

    test('store should return 422 when no current household exists', function () {
        // Arrange: on force HouseholdServiceContract à renvoyer null pour l’environnement HTTP
        $householdService = m::mock(HouseholdServiceContract::class);
        $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);
        app()->instance(HouseholdServiceContract::class, $householdService);

        // Act
        $response = $this->postJson(route('bills.store'), $this->payload);

        // Assert: mapping 422 + message d’erreur stable
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'error']);
        $response->assertJsonFragment(['message' => 'An error occurred while creating the bill']);
    });


    test('store should validates input (missing name => 422)', function () {
        $payload = $this->payload;
        unset($payload['name']);

        $response = $this->postJson(route('bills.store'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    });

    test('store should validates input (invalid distribution_method => 422)', function () {
        $payload = $this->payload;
        $payload['distribution_method'] = 'INVALID_METHOD';

        $response = $this->postJson(route('bills.store'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['distribution_method']);
    });
});

