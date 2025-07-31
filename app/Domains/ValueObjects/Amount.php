<?php

namespace App\Domains\ValueObjects;

use InvalidArgumentException;

class Amount
{

    private int $amount;

    public function __construct(int $amount)
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount must be a positive integer');
        }

        $this->amount = $amount;
    }

    public function value(): int
    {
        return $this->amount;
    }
}
