<?php

namespace Tests\Unit\Enums;

use App\Enums\DistributionMethod;

test('should obtain an array of values with the same number of cases', function () {
    expect(count(DistributionMethod::cases()))->toBe(count(DistributionMethod::values()));
});

test('should obtain an array of labels with the same number of cases', function() {
   expect(count(DistributionMethod::cases()))->toBe(count(DistributionMethod::labels()));
});

test('getDescriptions() should return an array with the same amount of cases', function() {
    expect(count(DistributionMethod::cases()))->toBe(count(DistributionMethod::descriptions()));
});
