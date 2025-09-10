<?php

namespace Tests\Feature\Services\Household;

use App\Models\Household;
use App\Services\Household\HouseholdService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('HouseholdService', function () {
    beforeEach(function () {
        $this->service = new HouseholdService();
    });

    describe('getHousehold()', function () {
        test('should return household when ID exists', function () {
            $household = Household::factory()->create(['name' => 'Test Household']);

            $result = $this->service->getHousehold($household->id);

            expect($result)->not->toBeNull()
                ->and($result->id)->toBe($household->id)
                ->and($result->name)->toBe('Test Household');
        });

        test('should return null when ID does not exist', function () {
            $result = $this->service->getHousehold(99999);

            expect($result)->toBeNull();
        });

        test('should return null when ID is invalid', function () {
            $result = $this->service->getHousehold(0);

            expect($result)->toBeNull();
        });
    });

    describe('getCurrentHousehold() - TEMPORARY IMPLEMENTATION', function () {
        test('should return null when no households exist', function () {
            $result = $this->service->getCurrentHousehold();

            expect($result)->toBeNull();
        });

        test('should return household when one exists', function () {
            $household = Household::factory()->create(['name' => 'Test Household']);

            $result = $this->service->getCurrentHousehold();

            expect($result)->not->toBeNull()
                ->and($result->id)->toBe($household->id)
                ->and($result->name)->toBe('Test Household');
        });

        // TODO: Ces tests devront être refactorisés quand l'authentification sera implémentée
        // Pour l'instant, teste le comportement actuel: orderBy('id')->first()
        test('should return first household by ID when multiple exist - TEMPORARY BEHAVIOR', function () {
            $household2 = Household::factory()->create(['name' => 'Second']);
            $household1 = Household::factory()->create(['name' => 'First']);

            $result = $this->service->getCurrentHousehold();

            expect($result)->not->toBeNull();
            // Teste que ça retourne le premier par ID, sans se soucier de l'ID exact
            $firstHousehold = Household::orderBy('created_at')->first();
            expect($result->id)->toBe($firstHousehold->id);
        });
    });
});
