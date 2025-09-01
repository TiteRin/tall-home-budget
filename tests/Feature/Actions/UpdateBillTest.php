<?php

namespace Tests\Feature\Actions;

use App\Actions\Bills\UpdateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Exceptions\Households\InvalidHouseholdException;
use App\Exceptions\Households\MismatchedHouseholdException;
use App\Services\Household\HouseholdServiceContract;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use ValueError;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->householdService = m::mock(HouseholdServiceContract::class);

    $this->household = bill_factory()->household(['has_joint_account' => true]);
    $this->member = bill_factory()->member([], $this->household);

    $this->updateAction = new UpdateBill($this->householdService);
});

afterEach(function () {
    m::close();
});

test('UpdateBill should throw an exception if the bill does not exist', function () {

    $this->householdService
        ->shouldReceive('getCurrentHousehold')->once()
        ->andReturn($this->household);

    $this->updateAction->handle(
        999,
        [
            'name' => 'Facture inexistante',
            'amount' => new Amount(10000),
            'distribution_method' => DistributionMethod::EQUAL,
            'member_id' => null
        ]
    );

})->throws(ModelNotFoundException::class);

describe("when updating an existing bill", function () {

    beforeEach(function () {
        $this->householdService
            ->shouldReceive('getCurrentHousehold')->once()
            ->andReturn($this->household);

        $this->bill = bill_factory()->bill([
            'name' => 'Facture à modifier',
            'amount' => new Amount(10000),
            'distribution_method' => DistributionMethod::EQUAL,
        ], $this->member, $this->household);
    });

    test('UpdateBill should update an existing bill with the partial values', function () {

        $this->updateAction->handle(
            $this->bill->id,
            [
                'distribution_method' => DistributionMethod::PRORATA,
            ]
        );

        $this->bill->refresh();

        expect($this->bill)->not->toBeNull()
            ->and($this->bill->name)->toBe('Facture à modifier')
            ->and($this->bill->distribution_method)->toBe(DistributionMethod::PRORATA);
    });

    test('should throw an exception when updating a bill with an invalid distribution method', function () {

        $this->updateAction->handle(
            $this->bill->id,
            [
                'distribution_method' => "invalid",
            ]
        );
    })->throws(ValueError::class);

    test('should throw an exception when updating a bill with an invalid amount', function () {
        $this->updateAction->handle(
            $this->bill->id,
            [
                'amount' => "invalid",
            ]
        );
    })->throws(Exception::class);

    test('should throw an exception when updating a bill with an invalid member', function () {
        $this->updateAction->handle(
            $this->bill->id,
            [
                'member_id' => 999,
            ]
        );
    })->throws(MismatchedHouseholdException::class);

    test('should throw an exception when updating a bill with an invalid household', function () {
        $this->updateAction->handle(
            $this->bill->id,
            [
                'household_id' => 999,
            ]
        );
    })->throws(ModelNotFoundException::class);

    test('should throw an exception when updating a bill if removing member and household has no joint account', function () {
        $this->household->update(['has_joint_account' => false]);
        $this->updateAction->handle(
            $this->bill->id,
            [
                'member_id' => null,
            ]
        );
    })->throws(InvalidHouseholdException::class);

    test('should update with valid values', function () {

        $this->updateAction->handle(
            $this->bill->id,
            [
                'distribution_method' => DistributionMethod::PRORATA,
                'name' => 'Nouveau nom',
                'amount' => new Amount(12000),
                'member_id' => null,
            ]
        );

        $this->bill->refresh();

        expect($this->bill)->not->toBeNull()
            ->and($this->bill->name)->toBe('Nouveau nom')
            ->and($this->bill->distribution_method)->toBe(DistributionMethod::PRORATA)
            ->and($this->bill->amount)->toEqual(new Amount(12000))
            ->and($this->bill->member_id)->toBeNull();
    });
});

