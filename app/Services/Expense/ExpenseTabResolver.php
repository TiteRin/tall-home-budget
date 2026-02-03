<?php

namespace App\Services\Expense;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\MonthlyPeriod;
use App\Models\ExpenseTab;
use Carbon\CarbonImmutable;

class ExpenseTabResolver
{

    public function __construct(
        protected readonly ExpenseTab $expenseTab)
    {
        ;
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

        if ($dateFrom->day < $this->expenseTab->from_day) {
            $dateFrom = $dateFrom->copy()->subMonth();
        }

        $endDayOfTheMonth = $this->expenseTab->from_day - 1;
        $currentMonth = $dateFrom->copy()->startOfMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        if ($endDayOfTheMonth < 1) {
            $nextMonth = $currentMonth->copy();
            $endDayOfTheMonth = $nextMonth->daysInMonth();
        }

        $from = CarbonImmutable::create($currentMonth->year, $currentMonth->month, $this->expenseTab->from_day);
        $to = CarbonImmutable::create($nextMonth->year, $nextMonth->month, $endDayOfTheMonth);

        if ($to->month !== $nextMonth->month) {
            $to = CarbonImmutable::create($nextMonth->year, $nextMonth->month, $nextMonth->daysInMonth());
        }

        return new MonthlyPeriod($from, $to);
    }

    public function getExpensesFor(MonthlyPeriod $monthlyPeriod): ExpensesCollection
    {
        return ExpensesCollection::from($this->expenseTab->expenses)->forMonthlyPeriod($monthlyPeriod);
    }

    public function getTotalAmountFor(MonthlyPeriod $monthlyPeriod): Amount
    {
        return $this->getExpensesFor($monthlyPeriod)->getTotal();
    }
}
