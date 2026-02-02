<?php

namespace App\Services\Expense;

use App\Domains\ValueObjects\Amount;
use App\Models\Expense;
use Illuminate\Support\Collection;

class ExpenseCollection extends Collection
{
    private function __construct(Collection $expenses)
    {
        // TODO : vérifier que tous les éléments de la collection sont des instances de Expense
        parent::__construct($expenses);
    }

    public static function from(Collection $expenses): self
    {
        return new self($expenses);
    }

    public function sum($callback = null): Amount
    {
        $amount = $this->reduce(function (Amount $carry, Expense $expense) {
            return $carry->add($expense->amount);
        }, new Amount(0));

        return $amount;
    }

}
