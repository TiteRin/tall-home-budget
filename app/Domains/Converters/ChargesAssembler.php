<?php

namespace App\Domains\Converters;

use App\Domains\ValueObjects\ChargesCollection;
use App\Services\Bill\BillsCollection;
use App\Services\Expense\ExpensesCollection;

final class ChargesAssembler
{
    private ChargesCollection $chargesCollection;

    public function __construct(
        private readonly BillToChargeConverter    $billToChargeConverter,
        private readonly ExpenseToChargeConverter $expenseToChargeConverter
    )
    {
        $this->chargesCollection = new ChargesCollection();
    }

    public function fromBills(BillsCollection $billsCollection)
    {
        foreach ($billsCollection as $bill) {
            $this->chargesCollection->add($this->billToChargeConverter->convert($bill));
        }

        return $this;
    }

    public function fromExpenses(ExpensesCollection $expenseCollection)
    {
        foreach ($expenseCollection as $expense) {
            $this->chargesCollection->add($this->expenseToChargeConverter->convert($expense));
        }

        return $this;
    }

    public function assemble(): ChargesCollection
    {
        return $this->chargesCollection;
    }

    public function reset(): static
    {
        $this->chargesCollection = new ChargesCollection();
        return $this;
    }

    public static function create(): static
    {
        return new self(
            new BillToChargeConverter(),
            new ExpenseToChargeConverter()
        );
    }
}
