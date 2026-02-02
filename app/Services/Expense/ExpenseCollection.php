<?php

namespace App\Services\Expense;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\MonthlyPeriod;
use App\Models\Expense;
use Illuminate\Support\Collection;

class ExpenseCollection extends Collection
{
    private ?MonthlyPeriod $monthlyPeriod = null;

    public function __construct(array $expenses)
    {
        foreach ($expenses as $expense) {
            if (!$expense instanceof Expense) {
                throw new \InvalidArgumentException('All elements must be instances of Expense');
            }
        }
        parent::__construct($expenses);
    }

    public static function from(Collection $expenses): self
    {
        return new self($expenses->all());
    }

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

}
