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
    test("Example 1", function () {
//        expect(true)->toBefalse();
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


