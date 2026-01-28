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
        // Happy Path : la période commence au 5, on veut la période qui correspond au 10 du mois => mois en cours
        // Grmbl Path : la période commence au 5, on veut la période qui correspond au 4 du mois => mois précédent
        // Argh Path : la période commence au 31, on veut la période qui correspond au 31, mais qui s’arrête au dernier jour du mois suivant => mois en cours

        if ($dateFrom->day < $this->startDayOfTheMonth) {
            $dateFrom = $dateFrom->copy()->subMonth();
        }

        $currentMonth = $dateFrom->copy()->startOfMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        $from = CarbonImmutable::create($currentMonth->year, $currentMonth->month, $this->startDayOfTheMonth);
        $to = CarbonImmutable::create($nextMonth->year, $nextMonth->month, $this->startDayOfTheMonth - 1);

        if ($to->month !== $nextMonth->month) {
            $to = CarbonImmutable::create($nextMonth->year, $nextMonth->month, $nextMonth->daysInMonth());
        }

        return new MonthlyPeriod($from, $to);
    }
}
