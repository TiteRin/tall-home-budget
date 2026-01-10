<?php

namespace App\Domains\ValueObjects;

use Illuminate\Support\Carbon;

class MonthlyPeriod
{
    public function __construct(public Carbon $start, public Carbon $end)
    {
    }
}
