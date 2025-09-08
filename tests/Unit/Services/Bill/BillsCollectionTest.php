<?php

namespace Tests\Unit\Services\Bill;

use App\Services\Bill\BillsCollection;

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
