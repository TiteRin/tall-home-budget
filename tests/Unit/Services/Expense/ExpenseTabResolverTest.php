<?php

namespace Tests\Unit\Services\Expense;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\MonthlyPeriod;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Services\Expense\ExpenseTabResolver;
use Carbon\CarbonImmutable;

describe("ExpenseTabResolver", function () {

    beforeEach(function () {
        $this->expenseTab = ExpenseTab::factory()->make([
            'from_day' => 5
        ]);
        $this->resolver = new ExpenseTabResolver($this->expenseTab);
    });

    describe("Monthly Period Resolution", function () {

        test("should return the current MonthlyPeriod", function () {
            $currentMonthlyPeriod = $this->resolver->getCurrentMonthlyPeriod();
            expect($currentMonthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($currentMonthlyPeriod->getFrom())->toBeInstanceOf(CarbonImmutable::class)
                ->and($currentMonthlyPeriod->getTo())->toBeInstanceOf(CarbonImmutable::class)
                ->and($currentMonthlyPeriod->contains(CarbonImmutable::now()));
        });

        test("given a date exactly at start, should return the current MonthlyPeriod", function () {
            $dateFrom = CarbonImmutable::create(2025, 4, 5);
            $expectedDateTo = CarbonImmutable::create(2025, 5, 4);
            $monthlyPeriod = $this->resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->contains($dateFrom))
                ->and($monthlyPeriod->getFrom())->toEqual($dateFrom)
                ->and($monthlyPeriod->getTo())->toEqual($expectedDateTo);
        });

        test("given a date exactly before start, should return the previous MonthlyPeriod", function () {
            $dateFrom = CarbonImmutable::create(2025, 2, 4);
            $expectedStart = CarbonImmutable::create(2025, 1, 5);
            $monthlyPeriod = $this->resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->contains($dateFrom))
                ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
                ->and($monthlyPeriod->getTo())->toEqual($dateFrom);
        });

        test("given a start of MonthlyPeriod the 31th, should return the last day of the month for the end of the MonthlyPeriod", function () {
            $resolver = new ExpenseTabResolver(ExpenseTab::factory()->make(['from_day' => 31]));
            $dateFrom = CarbonImmutable::create(2025, 1, 31);
            $expectedStart = CarbonImmutable::create(2025, 1, 31);
            $expectedEnd = CarbonImmutable::create(2025, 2, 28);
            $monthlyPeriod = $resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->contains($dateFrom))
                ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
                ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
        });

        test('given a start of MonthlyPeriod the 1th, should return the last day of the current month', function () {
            $resolver = new ExpenseTabResolver(ExpenseTab::factory()->make(['from_day' => 1]));
            $dateFrom = CarbonImmutable::create(2025, 1, 1);
            $expectedStart = CarbonImmutable::create(2025, 1, 1);
            $expectedEnd = CarbonImmutable::create(2025, 1, 31);
            $monthlyPeriod = $resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->contains($dateFrom))
                ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
                ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
        });

        test('given a date in the middle of a monthly period, should return the correct MonthlyPeriod', function () {
            $dateFrom = CarbonImmutable::create(2025, 4, 15);
            $expectedStart = CarbonImmutable::create(2025, 4, 5);
            $expectedEnd = CarbonImmutable::create(2025, 5, 4);
            $monthlyPeriod = $this->resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->contains($dateFrom))
                ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
                ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
        });

        test('given the last day of a monthly period, should return the correct MonthlyPeriod', function () {
            $dateFrom = CarbonImmutable::create(2025, 5, 4);
            $expectedStart = CarbonImmutable::create(2025, 4, 5);
            $expectedEnd = CarbonImmutable::create(2025, 5, 4);
            $monthlyPeriod = $this->resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->contains($dateFrom))
                ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
                ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
        });

        test('given first day of next period, should return the next MonthlyPeriod', function () {
            $dateFrom = CarbonImmutable::create(2025, 5, 5);
            $expectedStart = CarbonImmutable::create(2025, 5, 5);
            $expectedEnd = CarbonImmutable::create(2025, 6, 4);
            $monthlyPeriod = $this->resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->contains($dateFrom))
                ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
                ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
        });

        test('given from_day 29 spanning February in non-leap year, should adjust end date to last day of February', function () {
            $resolver = new ExpenseTabResolver(ExpenseTab::factory()->make(['from_day' => 29]));
            $dateFrom = CarbonImmutable::create(2025, 1, 29);
            $expectedStart = CarbonImmutable::create(2025, 1, 29);
            $expectedEnd = CarbonImmutable::create(2025, 2, 28);
            $monthlyPeriod = $resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
                ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
        });

        test('given from_day 30 spanning February, should adjust end date to last day of February', function () {
            $resolver = new ExpenseTabResolver(ExpenseTab::factory()->make(['from_day' => 30]));
            $dateFrom = CarbonImmutable::create(2025, 1, 30);
            $expectedStart = CarbonImmutable::create(2025, 1, 30);
            $expectedEnd = CarbonImmutable::create(2025, 2, 28);
            $monthlyPeriod = $resolver->getMonthlyPeriodFor($dateFrom);

            expect($monthlyPeriod)->toBeInstanceOf(MonthlyPeriod::class)
                ->and($monthlyPeriod->getFrom())->toEqual($expectedStart)
                ->and($monthlyPeriod->getTo())->toEqual($expectedEnd);
        });

    });

    describe("Expenses Filtering & Totals", function () {

        beforeEach(function () {
            $this->period = new MonthlyPeriod(
                CarbonImmutable::create(2025, 4, 5),
                CarbonImmutable::create(2025, 5, 4)
            );

            $this->hewisExpense = Expense::factory()->make([
                'name' => 'Hewey\'s Candy',
                'amount' => new Amount(1000),
                'spent_on' => CarbonImmutable::create(2025, 4, 10)
            ]);

            $this->deweyExpense = Expense::factory()->make([
                'name' => 'Dewey\'s Toy',
                'amount' => new Amount(2000),
                'spent_on' => CarbonImmutable::create(2025, 5, 4)
            ]);

            $this->louieExpense = Expense::factory()->make([
                'name' => 'Louie\'s Comic',
                'amount' => new Amount(3000),
                'spent_on' => CarbonImmutable::create(2025, 5, 5) // Outside (after)
            ]);

            $this->donaldExpense = Expense::factory()->make([
                'name' => 'Donald\'s Hat',
                'amount' => new Amount(4000),
                'spent_on' => CarbonImmutable::create(2025, 4, 4) // Outside (before)
            ]);

            $this->expenseTab->setRelation('expenses', collect([
                $this->hewisExpense,
                $this->deweyExpense,
                $this->louieExpense,
                $this->donaldExpense
            ]));
        });

        test('getExpensesFor should return ExpensesCollection filtered by given monthly period', function () {
            $expenses = $this->resolver->getExpensesFor($this->period);

            expect($expenses)->toBeInstanceOf(\App\Services\Expense\ExpensesCollection::class)
                ->and($expenses)->toHaveCount(2)
                ->and($expenses->first()->name)->toBe('Hewey\'s Candy')
                ->and($expenses->last()->name)->toBe('Dewey\'s Toy');
        });

        test('getTotalAmountFor should return correct Amount for given monthly period', function () {
            $totalAmount = $this->resolver->getTotalAmountFor($this->period);

            expect($totalAmount)->toBeInstanceOf(Amount::class)
                ->and($totalAmount->value())->toBe(3000);
        });

        test('getTotalAmountFor should return zero amount when no expenses in period', function () {
            $emptyPeriod = new MonthlyPeriod(
                CarbonImmutable::create(2024, 1, 1),
                CarbonImmutable::create(2024, 1, 31)
            );

            $totalAmount = $this->resolver->getTotalAmountFor($emptyPeriod);

            expect($totalAmount)->toBeInstanceOf(Amount::class)
                ->and($totalAmount)->toEqual(Amount::zero());
        });

    });

});
