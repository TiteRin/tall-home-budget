<?php

namespace Tests\Unit\ValueObjects;

use App\Domains\ValueObjects\MonthlyPeriod;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

describe("MonthlyPeriod", function () {

    test("should create a MonthlyPeriod with a start and a end date", function () {
        $monthlyPeriod = new MonthlyPeriod(CarbonImmutable::create(2025, 1, 1), CarbonImmutable::create(2025, 1, 31));
    });

    test("should throw an exception if end date is before start date", function () {
        new MonthlyPeriod(CarbonImmutable::create(2025, 1, 31), CarbonImmutable::create(2025, 1, 1));
    })->throws(InvalidArgumentException::class);

    test("when date is contained in monthly period, should return true", function () {

        $monthlyPeriod = new MonthlyPeriod(
            CarbonImmutable::create(2025, 1, 5),
            CarbonImmutable::create(2025, 2, 4)
        );
        expect($monthlyPeriod->contains(CarbonImmutable::create(2025, 1, 10)))->toBeTrue();
    });
});
