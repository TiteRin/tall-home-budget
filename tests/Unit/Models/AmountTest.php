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
})->throws(InvalidArgumentException::class, 'Amount [-1] must be a positive integer');

test('should format the amount for lisibility purposes', function () {
    $amount = new Amount(100000);
    expect($amount->__toString())->toBe('1 000,00 €');
});
