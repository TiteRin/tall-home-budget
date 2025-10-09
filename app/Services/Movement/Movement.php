<?php

namespace App\Services\Movement;

use App\Domains\ValueObjects\Amount;
use App\Models\Member;
use InvalidArgumentException;

;

class Movement
{
    public ?Member $memberFrom;
    public ?Member $memberTo;
    public Amount $amount;

    public function __construct(?Member $memberFrom, ?Member $memberTo, Amount $amount)
    {
        if ($memberTo === null && $memberFrom === null) {
            throw new InvalidArgumentException('No valid member');
        }

        if ($memberFrom && $memberTo && $memberFrom->id === $memberTo->id) {
            throw new InvalidArgumentException("Can’t transfer money to yourself");
        }

        if ($memberFrom && $memberTo && $memberFrom->household_id !== $memberTo->household_id) {
            throw new InvalidArgumentException('Members are not in the same household');
        }

        $this->memberFrom = $memberFrom;
        $this->memberTo = $memberTo;
        $this->amount = $amount;
    }
}
