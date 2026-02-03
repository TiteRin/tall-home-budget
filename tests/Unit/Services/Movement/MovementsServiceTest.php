<?php

namespace Tests\Unit\Services\Movement;

use App\Domains\Entities\JointAccount;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\User;
use App\Services\Bill\BillsCollection;
use App\Services\Movement\MovementsService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use InvalidArgumentException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->household = bill_factory()->household(['has_joint_account' => true]);
    $this->memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
    $this->memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);

    $this->members = collect([$this->memberAlice, $this->memberBob]);

    $this->bills = new BillsCollection([
        bill_factory()->bill(
            ['name' => 'Bill 1', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA],
            $this->memberAlice,
            $this->household
        ),
        bill_factory()->bill(
            ['name' => 'Bill 2', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA],
            $this->memberBob,
            $this->household
        ),
        bill_factory()->bill(
            ['name' => 'Bill 3', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA, 'member_id' => null],
            null,
            $this->household
        ),
    ]);

    $this->incomes = [
        $this->memberAlice->id => new Amount(200000),
        $this->memberBob->id => new Amount(200000),
    ];

    $user = User::factory()->create(['member_id' => $this->memberAlice->id]);
    $this->actingAs($user);
});

test('should return a collection of movements from bills and incomes', function () {
    $service = MovementsService::create()
        ->withMembers($this->members)
        ->withBills($this->bills)
        ->setIncomeFor($this->memberAlice, $this->incomes[$this->memberAlice->id])
        ->setIncomeFor($this->memberBob, $this->incomes[$this->memberBob->id]);

    expect($service->toMovements())->toBeInstanceOf(Collection::class);
});

describe('Validation around incomes', function () {
    test('toMovements should throw if no income have been set', function () {
        $service = MovementsService::create()
            ->withMembers($this->members)
            ->withBills($this->bills);

        $service->toMovements();
    })->throws(Exception::class, 'You need to set incomes for every member.');

    test('toMovements should throw if not every member has an income', function () {
        $service = MovementsService::create()
            ->withMembers($this->members)
            ->withBills($this->bills)
            ->setIncomeFor($this->memberAlice, new Amount(200000));

        $service->toMovements();
    })->throws(Exception::class, 'You need to set incomes for every member.');

    test('should not be possible to set an income for a non existing member', function () {
        $service = MovementsService::create()
            ->withMembers($this->members)
            ->withBills($this->bills);

        $service->setIncomeFor(bill_factory()->member(), new Amount(200000));
    })->throws(InvalidArgumentException::class);
});

describe('Example.md scenarios (via toMovements only)', function () {
    describe('Example 1', function () {
        beforeEach(function () {
            $household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => true]);

            $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $household);
            $memberBob = bill_factory()->member(['first_name' => 'Bob'], $household);

            $loyer = bill_factory()->bill([
                'name' => 'Loyer',
                'amount' => 70000,
                'distribution_method' => DistributionMethod::EQUAL,
                'member_id' => null,
            ], null, $household);

            $electricity = bill_factory()->bill([
                'name' => 'Électricité',
                'amount' => 9000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberAlice, $household);

            $internet = bill_factory()->bill([
                'name' => 'Internet',
                'amount' => 3000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberBob, $household);

            $veterinaire = bill_factory()->bill([
                'name' => 'Vétérinaire',
                'amount' => 10000,
                'distribution_method' => DistributionMethod::EQUAL,
            ], $memberBob, $household);

            $members = collect([$memberAlice, $memberBob]);
            $bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

            $this->movementService = MovementsService::create()
                ->withMembers($members)
                ->withBills($bills)
                ->setIncomeFor($memberAlice, new Amount(200000))
                ->setIncomeFor($memberBob, new Amount(100000));

            $this->memberAlice = $memberAlice;
            $this->memberBob = $memberBob;
        });

        test('toMovements()', function () {
            $movements = $this->movementService->toMovements();

            expect($movements)->toBeInstanceOf(Collection::class)
                ->and($movements)->toHaveCount(2)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBeInstanceOf(JointAccount::class)
                ->and($movements[0]->amount)->toEqual(new Amount(39000))
                ->and($movements[1]->memberFrom)->toBe($this->memberBob)
                ->and($movements[1]->memberTo)->toBeInstanceOf(JointAccount::class)
                ->and($movements[1]->amount)->toEqual(new Amount(31000));
        });
    });

    describe('Example 2', function () {
        beforeEach(function () {
            $household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => false]);

            $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $household);
            $memberBob = bill_factory()->member(['first_name' => 'Bob'], $household);
            $memberCharlie = bill_factory()->member(['first_name' => 'Charlie'], $household);

            $loyer = bill_factory()->bill([
                'name' => 'Loyer',
                'amount' => 70000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberCharlie, $household);

            $electricity = bill_factory()->bill([
                'name' => 'Électricité',
                'amount' => 9000,
                'distribution_method' => DistributionMethod::EQUAL,
            ], $memberAlice, $household);

            $internet = bill_factory()->bill([
                'name' => 'Internet',
                'amount' => 3000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberBob, $household);

            $veterinaire = bill_factory()->bill([
                'name' => 'Vétérinaire',
                'amount' => 10000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberAlice, $household);

            $members = collect([$memberAlice, $memberBob, $memberCharlie]);
            $bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

            $this->movementService = MovementsService::create()
                ->withMembers($members)
                ->withBills($bills)
                ->setIncomeFor($memberAlice, new Amount(200000))
                ->setIncomeFor($memberBob, new Amount(100000))
                ->setIncomeFor($memberCharlie, new Amount(200000));

            $this->memberAlice = $memberAlice;
            $this->memberBob = $memberBob;
            $this->memberCharlie = $memberCharlie;
        });

        test('toMovements()', function () {
            $movements = $this->movementService->toMovements();

            expect($movements)->toBeInstanceOf(Collection::class)
                ->and($movements)->toHaveCount(2)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[0]->amount)->toEqual(new Amount(17200))
                ->and($movements[1]->memberFrom)->toBe($this->memberBob)
                ->and($movements[1]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[1]->amount)->toEqual(new Amount(16600));
        });
    });

    describe('Example 3 - avec compte joint, mais négatif', function () {
        beforeEach(function () {
            $household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => true]);

            $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $household);
            $memberBob = bill_factory()->member(['first_name' => 'Bob'], $household);
            $memberCharlie = bill_factory()->member(['first_name' => 'Charlie'], $household);

            $loyer = bill_factory()->bill([
                'name' => 'Loyer',
                'amount' => 70000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberCharlie, $household);

            $electricity = bill_factory()->bill([
                'name' => 'Électricité',
                'amount' => 9000,
                'distribution_method' => DistributionMethod::EQUAL,
            ], $memberAlice, $household);

            $internet = bill_factory()->bill([
                'name' => 'Internet',
                'amount' => 3000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberBob, $household);

            $veterinaire = bill_factory()->bill([
                'name' => 'Vétérinaire',
                'amount' => 10000,
                'distribution_method' => DistributionMethod::PRORATA,
                'member_id' => null,
            ], null, $household);

            $members = collect([$memberAlice, $memberBob, $memberCharlie]);
            $bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

            $this->movementService = MovementsService::create()
                ->withMembers($members)
                ->withBills($bills)
                ->setIncomeFor($memberAlice, new Amount(200000))
                ->setIncomeFor($memberBob, new Amount(100000))
                ->setIncomeFor($memberCharlie, new Amount(200000));

            $this->memberAlice = $memberAlice;
            $this->memberBob = $memberBob;
            $this->memberCharlie = $memberCharlie;
        });

        test('toMovements()', function () {
            $movements = $this->movementService->toMovements();

            expect($movements)->toBeInstanceOf(Collection::class)
                ->and($movements)->toHaveCount(3)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[0]->amount)->toEqual(new Amount(27200))
                ->and($movements[1]->memberFrom)->toBe($this->memberBob)
                ->and($movements[1]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[1]->amount)->toEqual(new Amount(6600))
                ->and($movements[2]->memberFrom)->toBe($this->memberBob)
                ->and($movements[2]->memberTo)->toBeInstanceOf(JointAccount::class)
                ->and($movements[2]->amount)->toEqual(new Amount(10000));
        });
    });
});

describe('Income helpers', function () {
    test('should obtain the total of incomes', function () {
        $service = MovementsService::create()
            ->withMembers($this->members)
            ->withBills($this->bills)
            ->setIncomeFor($this->memberAlice, new Amount(200000))
            ->setIncomeFor($this->memberBob, new Amount(200000));

        $totalIncome = $service->getTotalIncome();

        expect($totalIncome)->toBeInstanceOf(Amount::class)
            ->and($totalIncome)->toEqual(new Amount(400000));
    });

    test('should obtain the ratios of incomes', function () {
        $service = MovementsService::create()
            ->withMembers($this->members)
            ->withBills($this->bills)
            ->setIncomeFor($this->memberAlice, new Amount(200000))
            ->setIncomeFor($this->memberBob, new Amount(200000));

        $ratios = $service->getRatiosFromIncome();

        expect($ratios)->toBeArray()
            ->and($ratios)->toHaveCount(2)
            ->and($ratios)->toMatchArray([
                $this->memberAlice->id => 0.5,
                $this->memberBob->id => 0.5,
            ]);
    });

    test('removeIncomeFor should make ratios fail again', function () {
        $service = MovementsService::create()
            ->withMembers($this->members)
            ->withBills($this->bills)
            ->setIncomeFor($this->memberAlice, new Amount(200000))
            ->setIncomeFor($this->memberBob, new Amount(200000));

        $service = $service->removeIncomeFor($this->memberBob);

        $service->getRatiosFromIncome();
    })->throws(Exception::class, 'You need to set incomes for every member.');
});
