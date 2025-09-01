<?php

namespace Tests\Feature\Repositories;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Repositories\Eloquent\EloquentBillRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new EloquentBillRepository();
    $this->household = bill_factory()->household();
    $this->member = bill_factory()->member([], $this->household);
});

test('create a bill in the database', function () {

    $bill = $this->repository->create(
        'Internet',
        new Amount(10000),
        DistributionMethod::PRORATA,
        $this->household->id,
        $this->member->id
    );

    expect($bill)->toBeInstanceOf(Bill::class);
    $this->assertDatabaseHas('bills', [
        'name' => 'Internet',
        'household_id' => $this->household->id,
    ]);
});

describe('get bills for household', function () {
    test('should return empty when no bills', function () {
        $bills = $this->repository->listForHousehold($this->household->id);
        expect($bills)->toBeEmpty();
    });

    test('should return correct numbers of bills', function () {
        bill_factory()->bills(4, [], $this->member, $this->household);
        bill_factory()->bills();
        $bills = $this->repository->listForHousehold($this->household->id);
        expect($bills)->toHaveCount(4);
    });
});

describe('get bills for member', function () {
    test('should return empty when no bills', function () {
        $bills = $this->repository->listForMember($this->member->id);
        expect($bills)->toBeEmpty();
    });

    test('should return correct numbers of bills', function () {
        bill_factory()->bills(4, [], $this->member, $this->household);
        bill_factory()->bills(2, [], null, $this->household);

        $bills = $this->repository->listForMember($this->member->id);

        expect($bills)->toHaveCount(4);
    });
});
