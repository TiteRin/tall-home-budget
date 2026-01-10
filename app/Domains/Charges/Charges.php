<?php

namespace App\Domains\Charges;

use App\Domains\ValueObjects\Amount;

final class Charges
{
    public function __construct(
        private Amount $billsTotal,
        private Amount $expensesTotal
    )
    {
    }

    public function billsTotal()
    {
        return $this->billsTotal;
    }

    public function expensesTotal()
    {
        return $this->expensesTotal;
    }

    public function total()
    {
        return $this->billsTotal->add($this->expensesTotal);
    }
}
