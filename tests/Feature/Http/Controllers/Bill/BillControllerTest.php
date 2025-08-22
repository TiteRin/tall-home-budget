<?php

namespace Tests\Feature\Http\Controllers\Bills;

use App\Enums\DistributionMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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

it('golden master: store returns 201 and success message with a bill object in payload', function () {
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

it('golden master: store validates input (missing name => 422)', function () {
    $payload = $this->payload;
    unset($payload['name']);

    $response = $this->postJson(route('bills.store'), $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

it('golden master: store validates input (invalid distribution_method => 422)', function () {
    $payload = $this->payload;
    $payload['distribution_method'] = 'INVALID_METHOD';

    $response = $this->postJson(route('bills.store'), $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['distribution_method']);
});
