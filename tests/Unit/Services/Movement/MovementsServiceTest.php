<?php

namespace Tests\Unit\Services\Movement;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Services\Bill\BillsCollection;
use App\Services\Movement\MovementsService;

beforeEach(function () {
    $this->household = bill_factory()->household(['has_joint_account' => true]);
    $this->memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
    $this->memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);
    $this->members = [$this->memberAlice, $this->memberBob];
    $this->bills = new BillsCollection([
        bill_factory()->bill(['name' => 'Bill 1', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA], $this->memberAlice, $this->household),
        bill_factory()->bill(['name' => 'Bill 2', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA], $this->memberBob, $this->household),
        bill_factory()->bill(['name' => 'Bill 3', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA], null, $this->household),
    ]);
    $this->incomes = [
        $this->memberAlice->id => new Amount(200000),
        $this->memberBob->id => new Amount(200000),
    ];
});

test('should return an array of movements from bills and incomes', function () {
    $service = new MovementsService(
        $this->members,
        $this->bills,
        $this->incomes,
    );

    expect($service->toMovements())->toBeArray();
});

describe("Example.md test", function () {

    describe("Example 1", function () {


        beforeEach(function () {
            $household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => true]);

            $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $household);
            $memberBob = bill_factory()->member(['first_name' => 'Bob'], $household);

            $loyer = bill_factory()->bill([
                'name' => 'Loyer',
                'amount' => 70000,
                'distribution_method' => DistributionMethod::EQUAL,
                'member_id' => null
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

            $members = [$memberAlice, $memberBob];
            $bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

            $incomes = [
                $memberAlice->id => new Amount(200000),
                $memberBob->id => new Amount(100000),
            ];

            $this->movementService = new MovementsService($members, $bills, $incomes);
            $this->memberAlice = $memberAlice;
            $this->memberBob = $memberBob;
        });

        test("computeBalance()", function () {
            $balances = $this->movementService->computeBalances();

            expect($balances)->toHaveCount(2)
                ->and($balances->first()->member)->toBe($this->memberAlice)
                ->and($balances->first()->amount)->toEqual(new Amount(-39000))
                ->and($balances->last()->member)->toBe($this->memberBob)
                ->and($balances->last()->amount)->toEqual(new Amount(-31000));
        });

        test("toMovements()", function () {

            $movements = $this->movementService->toMovements();
            expect($movements)->toBeArray()
                ->and($movements)->toHaveCount(2)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBeNull()
                ->and($movements[0]->amount)->toEqual(new Amount(39000))
                ->and($movements[1]->memberFrom)->toBe($this->memberBob)
                ->and($movements[1]->memberTo)->toBeNull()
                ->and($movements[1]->amount)->toEqual(new Amount(31000));
        });
    });

    describe("Example 2", function () {

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

            $members = [$memberAlice, $memberBob, $memberCharlie];
            $bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

            $incomes = [
                $memberAlice->id => new Amount(200000),
                $memberBob->id => new Amount(100000),
                $memberCharlie->id => new Amount(200000),
            ];

            $this->movementService = new MovementsService($members, $bills, $incomes);
            $this->memberAlice = $memberAlice;
            $this->memberBob = $memberBob;
            $this->memberCharlie = $memberCharlie;
        });

        test("computeBalances()", function () {
            $balances = $this->movementService->computeBalances();

            expect($balances)->toHaveCount(3)
                ->and($balances[0]->member)->toBe($this->memberAlice)
                ->and($balances[0]->amount)->toEqual(new Amount(-17200))
                ->and($balances[1]->member)->toBe($this->memberBob)
                ->and($balances[1]->amount)->toEqual(new Amount(-16600))
                ->and($balances[2]->member)->toBe($this->memberCharlie)
                ->and($balances[2]->amount)->toEqual(new Amount(33800));
        });

        test('toMovements()', function () {

            $movements = $this->movementService->toMovements();
            expect($movements)->toBeArray()
                ->and($movements)->toHaveCount(2)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[0]->amount)->toEqual(new Amount(17200))
                ->and($movements[1]->memberFrom)->toBe($this->memberBob)
                ->and($movements[1]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[1]->amount)->toEqual(new Amount(16600));
        });
    });
});

test('should obtain the bills total amount in an array', function () {
    $service = new MovementsService(
        $this->members,
        $this->bills,
        $this->incomes,
    );

    $totals = $service->getTotalsAmount();
    expect($totals)->toBeArray()
        ->and($totals)->toHaveCount(3)
        ->and($totals)->toMatchArray(
            [
                'total' => new Amount(30000),
                'prorata' => new Amount(30000),
                'equal' => new Amount(0),
            ]
        );
});

test("should obtain the total of incomes", function () {
    $service = new MovementsService(
        $this->members,
        $this->bills,
        [
            $this->memberAlice->id => new Amount(200000),
            $this->memberBob->id => new Amount(200000),
        ],
    );

    $totalIncome = $service->getTotalIncome();
    expect($totalIncome)->toBeInstanceOf(Amount::class)
        ->and($totalIncome)->toEqual(new Amount(400000));;
});

test('should obtain the ratios of incomes', function () {
    $service = new MovementsService(
        $this->members,
        $this->bills,
        [
            $this->memberAlice->id => new Amount(200000),
            $this->memberBob->id => new Amount(200000),
        ],
    );

    $ratios = $service->getRatiosFromIncome();

    expect($ratios)->toBeArray()
        ->and($ratios)->toHaveCount(2)
        ->and($ratios)->toMatchArray(
            [
                $this->memberAlice->id => 0.5,
                $this->memberBob->id => 0.5,
            ]
        );
});


