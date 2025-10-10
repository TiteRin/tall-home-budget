<?php

namespace Tests\Unit\Models;

use App\Domains\Entities\JointAccount;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Household;

describe('Household', function () {

    test('can create household', function () {
        $household = bill_factory()->household([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $this->assertDatabaseHas('households', $household->toArray());
    });

    test('can update household', function () {
        $household = bill_factory()->household([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $household->update([
            'name' => 'Updated Household',
            'has_joint_account' => true,
            'default_distribution_method' => DistributionMethod::PRORATA,
        ]);

        $this->assertDatabaseHas('households', [
            'id' => $household->id,
            'name' => 'Updated Household',
            'has_joint_account' => true,
            'default_distribution_method' => DistributionMethod::PRORATA->value,
        ]);
    });

    test('can delete household', function () {
        $household = bill_factory()->household([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $household->delete();

        $this->assertDatabaseMissing('households', $household->toArray());
    });
});

describe('Household members', function () {

    beforeEach(function () {
        $this->household = bill_factory()->household([
            'name' => 'Test Household',
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);
    });

    test('can add household member', function () {
        $this->household->members()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertDatabaseHas('members', [
            'household_id' => $this->household->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    });

    test('can get household members', function () {
        bill_factory()->members(3, [], $this->household);
        expect($this->household->members()->count())->toBe(3);
    });

    test('can get household\’s default distribution method', function () {
        expect($this->household->getDefaultDistributionMethod())->toEqual(DistributionMethod::EQUAL);
    });

    test('can delete household member', function () {
        $this->household->members()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->household->members()->where('first_name', 'John')->delete();

        $this->assertDatabaseMissing('members', [
            'household_id' => $this->household->id,
            'first_name' => 'John',
        ]);
    });

    describe('Household with members', function () {

        beforeEach(function () {
            $this->members = bill_factory()->members(3, [], $this->household);
        });

        test('household start with 0 amount', function () {
            expect($this->household->total_amount)->toEqual(new Amount(0));
        });

        test('household start with 0 bills', function () {
            expect($this->household->bills()->count())->toBe(0);
        });

        test('when household has bill, total amount should be the sum of the bills', function () {
            bill_factory()->bill([
                'amount' => 10000
            ], $this->members->first());
            bill_factory()->bill([
                'amount' => 3000
            ], $this->members->last());

            expect($this->household->total_amount)->toEqual(new Amount(13000))
                ->and($this->household->total_amount_formatted)->toBe((new Amount(13000))->toCurrency());
        });
    });
});

describe("With or without a joint account", function () {
    it("should return a JointAccount entity", function () {
        $household = Household::factory()->create(['has_joint_account' => true]);
        expect($household->jointAccount())->toBeInstanceOf(JointAccount::class);
    });

    it("should return null", function () {
        $household = Household::factory()->create(['has_joint_account' => false]);
        expect($household->jointAccount())->toBeNull();
    });
});
