<?php

namespace App\Domains\Converters;

use App\Domains\ValueObjects\Charge;
use App\Models\Expense;

final class ExpenseToChargeConverter
{
    public function convert(Expense $expense): Charge
    {
        return Charge::create()
            ->withAmount($expense->amount)
            ->withDistributionMethod($expense->distribution_method)
            ->withPayer($expense->member ?? null);
    }
}
