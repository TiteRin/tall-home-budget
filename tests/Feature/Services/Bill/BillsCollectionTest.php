<?php

namespace Tests\Feature\Services\Bill;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Services\Bill\BillsCollection;
use Exception;

beforeEach(function () {
    $this->household = bill_factory()->household(['has_joint_account' => true]);
    $this->memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
    $this->memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);
});

test("should initialize with empty array", function () {
    $collection = new BillsCollection([]);

    expect($collection->toArray())->toBeEmpty();
});

test("should initialize with array of bills", function () {
    $collection = new BillsCollection(bill_factory()->bills(3, [], $this->memberAlice, $this->household));

    expect($collection->toArray())->toHaveCount(3);
});

test("should initialize with a collection of bills", function () {
    $bills = collect(bill_factory()->bills(3, [], $this->memberAlice, $this->household));
    $collection = new BillsCollection($bills);

    expect($collection->toArray())->toHaveCount(3);
});

test("should add a bill to the collection", function () {
    $bills = collect(bill_factory()->bills(3, [], $this->memberAlice, $this->household));
    $collection = new BillsCollection($bills);

    $collection->add(bill_factory()->bill([], $this->memberBob, $this->household));
    expect($collection->toArray())->toHaveCount(4);
});

test("should not add a bill to the collection if it already exists", function () {
    $bills = collect(bill_factory()->bills(3, [], $this->memberAlice, $this->household));
    $collection = new BillsCollection($bills);

    $collection->add($bills->first());
})->throws(Exception::class, "Bill already exists");

describe("Summarization", function () {

    test("should obtain the total amount of the collection", function () {
        $collection = new BillsCollection(
            [
                bill_factory()->bill(['name' => 'Internet', 'amount' => 3000], $this->memberAlice, $this->household),
                bill_factory()->bill(['name' => 'Loyer', 'amount' => 67000], null, $this->household),
                bill_factory()->bill(['name' => 'Électricité', 'amount' => 10000], $this->memberBob, $this->household),
            ]
        );

        expect($collection->getTotal())->toEqual(new Amount(67000 + 3000 + 10000));
    });

    test("should obtain the total amount of the collection for a distribution method", function () {
        $collection = new BillsCollection(
            [
                bill_factory()->bill(['name' => 'Internet', 'amount' => 3000, 'distribution_method' => DistributionMethod::PRORATA], $this->memberAlice, $this->household),
                bill_factory()->bill(['name' => 'Loyer', 'amount' => 67000, 'distribution_method' => DistributionMethod::EQUAL], null, $this->household),
                bill_factory()->bill(['name' => 'Électricité', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA], $this->memberBob, $this->household),
            ]
        );

        expect($collection->getTotalForDistributionMethod(DistributionMethod::PRORATA))->toEqual(new Amount(3000 + 10000))
            ->and($collection->getTotalForDistributionMethod(DistributionMethod::EQUAL))->toEqual(new Amount(67000));
    });

    test("should obtain the total for a member", function () {
        $collection = new BillsCollection(
            [
                bill_factory()->bill(['name' => 'Internet', 'amount' => 3000, 'distribution_method' => DistributionMethod::PRORATA], $this->memberAlice, $this->household),
                bill_factory()->bill(['name' => 'Loyer', 'amount' => 67000, 'distribution_method' => DistributionMethod::EQUAL, 'member_id' => null], null, $this->household),
                bill_factory()->bill(['name' => 'Électricité', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA], $this->memberBob, $this->household),
            ]
        );

        expect($collection->getTotalForMember($this->memberAlice))->toEqual(new Amount(3000))
            ->and($collection->getTotalForMember($this->memberBob))->toEqual(new Amount(10000))
            ->and($collection->getTotalForMember(null))->toEqual(new Amount(67000));
    });
});
