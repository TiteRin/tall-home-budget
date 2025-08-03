<?php

namespace Tests\Unit\Models;

use App\Domains\ValueObjects\Amount;
use App\Models\Bill;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('bill amount is an Amount object', function () {
    $bill = new Bill();
    $bill->amount = 17900;
    expect($bill->amount)
        ->toBeInstanceOf(Amount::class)
        ->and($bill->amount->value())
        ->toBe(17900);
});


test('bill amount is always positive', function () {
    $bill = new Bill();
    $bill->amount = -17900;

    $amount = $bill->amount;
})->throws(\InvalidArgumentException::class);
