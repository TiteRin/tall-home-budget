<?php

namespace App\Domains\ValueObjects;

class Amount
{

    private int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function value(): int
    {
        return $this->amount;
    }
}
