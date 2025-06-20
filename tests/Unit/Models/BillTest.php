<?php

use App\Models\Bill;

test('can create a bill model instance', function () {
    $bill = new Bill();
    expect($bill)->toBeInstanceOf(Bill::class);
});