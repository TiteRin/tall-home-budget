<?php

namespace Tests\Unit\Services\Expense;

use App\Domains\ValueObjects\MonthlyPeriod;
use App\Services\Expense\ExpenseServiceResolver;
use Carbon\CarbonImmutable;

describe("ExpenseServiceResolver", function () {
    test("should return the current MonthlyPeriod", function () {
        $expenseServiceResolver = new ExpenseServiceResolver(5);
        $currentMonthlyPeriod = $expenseServiceResolver->getCurrentMonthlyPeriod();
        expect($currentMonthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
            ->and($currentMonthlyPeriod->getFrom())->toBeInstanceOf(CarbonImmutable::class)
            ->and($currentMonthlyPeriod->getTo())->toBeInstanceOf(CarbonImmutable::class)
            ->and($currentMonthlyPeriod->contains(CarbonImmutable::now()));
    });

    test("given a date exactly at start, should return the current MonthlyPeriod", function () {

        $dateFrom = CarbonImmutable::create(2025, 2, 5);
        $expenseServiceResolver = new ExpenseServiceResolver(5);
        $monthlyPeriod = $expenseServiceResolver->getMonthlyPeriodFor($dateFrom);
        expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
            ->and($monthlyPeriod->contains($dateFrom))
            ->and($monthlyPeriod->getFrom())->toEqual($dateFrom);
    });

    test("given a date exactly before start, should return the previous MonthlyPeriod", function () {
        $dateFrom = CarbonImmutable::create(2025, 2, 4);
        $expectedStart = CarbonImmutable::create(2025, 1, 5);
        $expenseServiceResolver = new ExpenseServiceResolver(5);
        $monthlyPeriod = $expenseServiceResolver->getMonthlyPeriodFor($dateFrom);
        expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
            ->and($monthlyPeriod->contains($dateFrom))
            ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
            ->and($monthlyPeriod->getTo())->toEqual($dateFrom);
    });
});
