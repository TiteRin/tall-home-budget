<?php

namespace Tests\Unit\Models;

use App\Domains\ValueObjects\Amount;

test('should represent an amount', function () {
    $amount = new Amount(1000);
    expect($amount->value())->toBe(1000);
});
