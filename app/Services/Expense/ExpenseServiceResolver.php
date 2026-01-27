<?php

namespace App\Services\Expense;

use App\Domains\ValueObjects\MonthlyPeriod;
use Carbon\CarbonImmutable;

class ExpenseServiceResolver
{
    private int $startDayOfTheMonth;

    public function __construct(int $startDayOfTheMonth)
    {
        $this->startDayOfTheMonth = $startDayOfTheMonth;
    }

    public function getCurrentMonthlyPeriod(): MonthlyPeriod
    {
        $now = CarbonImmutable::now();

        $from = CarbonImmutable::create($now->year, $now->month, $this->startDayOfTheMonth);
        $to = CarbonImmutable::create($now->year, $now->month + 1, $this->startDayOfTheMonth - 1);

        return new MonthlyPeriod($from, $to);
    }
}
