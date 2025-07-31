<?php

namespace Tests\Unit\Models;

use App\Domains\ValueObjects\Amount;
use InvalidArgumentException;

test('should represent an amount', function () {
    $amount = new Amount(1000);
    expect($amount->value())->toBe(1000);
});

test('should always be positive', function () {
    $amount = new Amount(-1);
})->throws(InvalidArgumentException::class, 'Amount must be a positive integer');
