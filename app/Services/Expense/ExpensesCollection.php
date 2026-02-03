<?php

namespace App\Services\Expense;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\MonthlyPeriod;
use App\Enums\DistributionMethod;
use App\Models\Expense;
use App\Models\Member;
use App\Support\Collections\TypedCollection;

class ExpensesCollection extends TypedCollection
{
    public function forMonthlyPeriod(MonthlyPeriod $monthlyPeriod)
    {
        $this->monthlyPeriod = $monthlyPeriod;
        return $this
            ->filter(function (Expense $expense) use ($monthlyPeriod) {
                if ($monthlyPeriod === null) {
                    return true;
                }
                return $monthlyPeriod->contains($expense->spent_on);
            });
    }

    public function getTotal(): Amount
    {
        $amount = $this
            ->reduce(function (Amount $carry, Expense $expense) {
                return $carry->add($expense->amount);
            }, new Amount(0));

        return $amount;
    }

    public function getTotalForDistributionMethod(DistributionMethod $distributionMethod): Amount
    {
        $filtered = $this->filter(function (Expense $expense) use ($distributionMethod) {
            return $expense->distribution_method === $distributionMethod;
        });

        return $filtered->getTotal();
    }

    public function getTotalForMember(Member $member): Amount
    {
        return $this->filter(fn(Expense $expense) => $expense->member_id === $member->id)->getTotal();
    }

    public function getTotalForJointAccount(): Amount
    {
        return $this->filter(fn(Expense $expense) => $expense->member_id === null)->getTotal();
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
