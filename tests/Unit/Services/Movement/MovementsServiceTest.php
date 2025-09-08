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
        $this->memberAlice->id => 200000,
        $this->memberBob->id => 200000,
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


