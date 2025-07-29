<?php

use App\Enums\DistributionMethod;

test('should obtain an array of labels with the same number of cases', function() {
   expect(count(DistributionMethod::cases()))->toBe(count(DistributionMethod::labels()));
});
