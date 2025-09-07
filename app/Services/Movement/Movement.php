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

    public function reduce(Movement $movement): array
    {
        if (!$this->hasCommonMember($movement)) {
            return [$this, $movement];
        }

        if ($this->memberFrom->id === $movement->memberFrom->id
            && $this->memberTo->id === $movement->memberTo->id) {
            return [
                new Movement($this->memberFrom, $this->memberTo, new Amount($this->amount->value() + $movement->amount->value()))
            ];
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

        if ($this->amount->__equals($movement->amount)) {

            if ($this->memberFrom->id === $movement->memberTo->id) {
                return [
                    new Movement($movement->memberFrom, $this->memberTo, $this->amount)
                ];
            }

            return [
                new Movement($this->memberFrom, $movement->memberTo, $this->amount)
            ];
        }

        // trouver la direction du mouvement
        $memberFrom = $memberTo = $intermediary = $amount = null;

        // AB and BC => AC / B
        if ($this->memberTo->id === $movement->memberFrom->id) {
            $memberFrom = $this->memberFrom;
            $memberTo = $movement->memberTo;
            $intermediary = $movement->memberFrom;
            $amount = $this->amount->value() - $movement->amount->value();
        }
        // AB and CA => CB / A
        if ($this->memberFrom->id === $movement->memberTo->id) {
            $memberFrom = $movement->memberFrom;
            $memberTo = $this->memberTo;
            $intermediary = $this->memberFrom;
            $amount = $movement->amount->value() - $this->amount->value();
        }

        // [AB100, BC100] = [AC100]
        // [AB100, CA100] = [CB100]
        if ($amount === 0) {
            return [
                new Movement($memberFrom, $memberTo, $this->amount),
            ];
        }

        // [AB300, BC100] => [AB200, AC100]
        // [AB100, CA300] => [CA300, AB100] => [CA200, CB100]
        if ($amount > 0) {

            $maxAmount = max($this->amount->value(), $movement->amount->value());
            $leftAmount = $maxAmount - $amount;

            return [
                new Movement($memberFrom, $intermediary, new Amount($amount)),
                new Movement($memberFrom, $memberTo, new Amount($leftAmount))
            ];
        }

        // [AB50, BC100] => idem
        // [AB100, CA50] => [CA50, AB100] => idem
        return [
            $this,
            $movement
        ];
    }
}
