<?php

namespace App\Domains\ValueObjects;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

class MonthlyPeriod
{
    private CarbonImmutable $from;
    private CarbonImmutable $to;

    public function __construct(CarbonImmutable $from, CarbonImmutable $end)
    {
        if ($from->gt($end)) {
            throw new InvalidArgumentException("Start date cannot be after end date");
        }

        $this->from = $from;
        $this->to = $end;
    }

    public function contains($date): bool
    {
        return $this->from->lte($date) && $this->to->gte($date);
    }
}
