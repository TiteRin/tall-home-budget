<?php

namespace App\Services\Expense;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\MonthlyPeriod;
use App\Models\Expense;
use App\Support\Collections\TypedCollection;

class ExpenseCollection extends TypedCollection
{
    private ?MonthlyPeriod $monthlyPeriod = null;


    public function forMonthlyPeriod(MonthlyPeriod $monthlyPeriod)
    {
        $this->monthlyPeriod = $monthlyPeriod;
        return $this;
    }

    public function resetMonthlyPeriod()
    {
        $this->monthlyPeriod = null;
        return $this;
    }

    public function sum($callback = null): Amount
    {
        $monthlyPeriod = $this->monthlyPeriod;
        $amount = $this
            ->filter(function (Expense $expense) use ($monthlyPeriod) {
                if ($monthlyPeriod === null) {
                    return true;
                }
                return $monthlyPeriod->contains($expense->spent_on);
            })
            ->reduce(function (Amount $carry, Expense $expense) {
                return $carry->add($expense->amount);
            }, new Amount(0));

        return $amount;
    }


    protected function getExpectedType(): string
    {
        return Expense::class;
    }

    protected function getCollectionName(): string
    {
        return self::class;
    }
}
