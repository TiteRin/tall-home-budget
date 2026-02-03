<?php

namespace Tests\Unit\Services\Expense;

use App\Domains\ValueObjects\MonthlyPeriod;
use App\Models\ExpenseTab;
use App\Services\Expense\ExpenseTabResolver;
use Carbon\CarbonImmutable;

describe("ExpenseTabResolver", function () {

    beforeEach(function () {
        $this->expenseTab = ExpenseTab::factory()->make([
            'from_day' => 5
        ]);
    });

    test("should return the current MonthlyPeriod", function () {
        $expenseServiceResolver = new ExpenseTabResolver($this->expenseTab);
        $currentMonthlyPeriod = $expenseServiceResolver->getCurrentMonthlyPeriod();
        expect($currentMonthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
            ->and($currentMonthlyPeriod->getFrom())->toBeInstanceOf(CarbonImmutable::class)
            ->and($currentMonthlyPeriod->getTo())->toBeInstanceOf(CarbonImmutable::class)
            ->and($currentMonthlyPeriod->contains(CarbonImmutable::now()));
    });

    test("given a date exactly at start, should return the current MonthlyPeriod", function () {

        $expenseServiceResolver = new ExpenseTabResolver($this->expenseTab);

        $dateFrom = CarbonImmutable::create(2025, 4, 5);
        $expectedDateTo = CarbonImmutable::create(2025, 5, 4);
        $monthlyPeriod = $expenseServiceResolver->getMonthlyPeriodFor($dateFrom);
        expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
            ->and($monthlyPeriod->contains($dateFrom))
            ->and($monthlyPeriod->getFrom())->toEqual($dateFrom)
            ->and($monthlyPeriod->getTo())->toEqual($expectedDateTo);
    });

    test("given a date exactly before start, should return the previous MonthlyPeriod", function () {

        $expenseServiceResolver = new ExpenseTabResolver($this->expenseTab);

        $dateFrom = CarbonImmutable::create(2025, 2, 4);
        $expectedStart = CarbonImmutable::create(2025, 1, 5);
        $monthlyPeriod = $expenseServiceResolver->getMonthlyPeriodFor($dateFrom);
        expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
            ->and($monthlyPeriod->contains($dateFrom))
            ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
            ->and($monthlyPeriod->getTo())->toEqual($dateFrom);
    });

    test("given a start of MonthlyPeriod the 31th, should return the last day of the month for the end of the MonthlyPeriod", function () {

        $expenseServiceResolver = new ExpenseTabResolver(ExpenseTab::factory()->make(['from_day' => 31]));
        $dateFrom = CarbonImmutable::create(2025, 1, 31);
        $expectedStart = CarbonImmutable::create(2025, 1, 31);
        $expectedEnd = CarbonImmutable::create(2025, 2, 28);
        $monthlyPeriod = $expenseServiceResolver->getMonthlyPeriodFor($dateFrom);
        expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
            ->and($monthlyPeriod->contains($dateFrom))
            ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
            ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
    });

    test('given a start of MonthlyPeriod the 1th, should return the last day of the current month', function () {
        $expenseServiceResolver = new ExpenseTabResolver(ExpenseTab::factory()->make(['from_day' => 1]));
        $dateFrom = CarbonImmutable::create(2025, 1, 1);
        $expectedStart = CarbonImmutable::create(2025, 1, 1);
        $expectedEnd = CarbonImmutable::create(2025, 1, 31);
        $monthlyPeriod = $expenseServiceResolver->getMonthlyPeriodFor($dateFrom);
        expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
            ->and($monthlyPeriod->contains($dateFrom))
            ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
            ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
    });
});
