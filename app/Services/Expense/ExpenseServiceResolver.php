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
        return $this->getMonthlyPeriodFor($now);
    }

    public function getMonthlyPeriodFor(CarbonImmutable $dateFrom): MonthlyPeriod
    {
        if ($dateFrom->day < $this->startDayOfTheMonth) {
            $dateFrom = $dateFrom->copy()->subMonth();
        }

        $from = CarbonImmutable::create($dateFrom->year, $dateFrom->month, $this->startDayOfTheMonth);
        $to = CarbonImmutable::create($dateFrom->year, $dateFrom->month + 1, $this->startDayOfTheMonth - 1);

        return new MonthlyPeriod($from, $to);
    }
}
