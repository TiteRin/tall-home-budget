<?php

namespace App\Domains\Converters;

use App\Domains\ValueObjects\Charge;
use App\Models\Bill;

final class BillToChargeConverter
{
    public function convert(Bill $bill): Charge
    {
        return Charge::create()
            ->withAmount($bill->amount)
            ->withDistributionMethod($bill->distribution_method)
            ->withPayer($bill->member ?? null);
    }
}
