<?php

namespace App\Tests\Unit\Models;

use App\Models\Bill;

test('can create a bill model instance', function () {
    $bill = new Bill();
    expect($bill)->toBeInstanceOf(Bill::class);
});

test('can get the formatted amount', function () {
    $bill = new Bill();
    $bill->amount = 17900;
    expect($bill->amount_formatted)->toBe('179,00 €');
});