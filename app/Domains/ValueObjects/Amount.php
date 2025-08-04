<?php

namespace App\Domains\ValueObjects;

use App\Traits\HasCurrencyFormatting;
use InvalidArgumentException;

class Amount
{

    use HasCurrencyFormatting;

    private int $amount;

    public function __construct(int $amount)
    {
        if ($amount < 0) {
            throw new InvalidArgumentException("Amount [$amount] must be a positive integer");
        }

        $this->amount = $amount;
    }

    public function value(): int
    {
        return $this->amount;
    }


    public function __toString(): string
    {
        return $this->formatCurrency($this->amount);
    }

    public function __equals(Amount $amount): bool
    {
        return $this->value() === $amount->value();
    }

    public static function from(string $amount): self
    {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException("Amount [$amount] must be a number");
        }

        return new Amount((int)round((float)$amount * 100));
    }
}
