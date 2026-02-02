<?php

namespace Tests\Unit\Models;

use App\Domains\ValueObjects\Amount;
use App\Models\Expense;
use App\Models\ExpenseTab;
use Carbon\CarbonImmutable;

describe("Expense Tab", function () {

    test('should give the total of its expenses', function () {
        $expenseTab = ExpenseTab::factory()->make();
        $expenses = Expense::factory()
            ->count(10)
            ->make([
                'amount' => new Amount(1000),
            ]);

        $expenseTab->setRelation('expenses', $expenses);

        expect($expenseTab->totalAmount())
            ->toBeInstanceOf(Amount::class)
            ->toEqual(new Amount(10000));
    });

    test('should give the total for a monthly period', function () {

        $now = CarbonImmutable::now();

        $expenseTab = ExpenseTab::factory()->make([
            'from_day' => ($now->day + 5) % $now->daysInMonth // mois en cours jusqu’à aujourd’hui + 5 jours
        ]);

        $expensesCurrentPeriod = Expense::factory()
            ->count(5)
            ->make([
                'amount' => new Amount(1000),
                'spent_on' => $now->subDays(random_int(0, 10))
            ]);

        $expensesNextPeriod = Expense::factory()
            ->count(5)
            ->make([
                'amount' => new Amount(1000),
                'spent_on' => $now->addDays(random_int(6, 15))
            ]);

        $allExpenses = collect();
        $allExpenses = $allExpenses->merge($expensesCurrentPeriod)->merge($expensesNextPeriod);


        $expenseTab->setRelation('expenses', $allExpenses);

        expect($expenseTab->totalAmount())->toEqual(new Amount(10000))
            ->and($expenseTab->totalAmountForCurrentPeriod())->toEqual(new Amount(5000));
    });
});
