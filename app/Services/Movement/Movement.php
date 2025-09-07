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

        if ($memberFrom && $memberTo === null && !$memberFrom->household->has_joint_account) {
            throw new InvalidArgumentException('No joint account to transfer to');
        }

        if ($memberTo && $memberFrom === null && !$memberTo->household->has_joint_account) {
            throw new InvalidArgumentException('No joint account to transfer from');
        }

        $this->memberFrom = $memberFrom;
        $this->memberTo = $memberTo;
        $this->amount = $amount;
    }

    public function hasCommonMember(Movement $movement): bool
    {
        return $this->memberFrom->id === $movement->memberFrom->id
            || $this->memberTo->id === $movement->memberTo->id
            || $this->memberFrom->id === $movement->memberTo->id
            || $this->memberTo->id === $movement->memberFrom->id;
    }

    public function sum(Movement $movement): array
    {
        if (!$this->hasCommonMember($movement)) {
            return [$this, $movement];
        }

        if ($this->memberTo->id === $movement->memberTo->id) {
            return [$this, $movement];
        }

        if ($this->memberFrom->id === $movement->memberFrom->id) {
            return [$this, $movement];
        }

        if ($this->memberFrom->id === $movement->memberTo->id
            && $this->memberTo->id === $movement->memberFrom->id) {

            $amount = $this->amount->value() - $movement->amount->value();

            if ($amount === 0) {
                return [];
            }

            if ($amount > 0) {
                return [new Movement($this->memberFrom, $this->memberTo, new Amount($amount))];
            }

            return [new Movement($this->memberTo, $this->memberFrom, new Amount(-$amount))];
        }

        return [
            new Movement($this->memberFrom, $this->memberTo, new Amount(10000)),
            new Movement($this->memberFrom, $movement->memberTo, new Amount(15000))
        ];
    }
}
