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
});
