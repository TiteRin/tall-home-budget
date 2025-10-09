<?php

namespace App\Domains\ValueObjects;

use App\Models\Member;

class Balance
{

    public Member $member;
    public Amount $amount;

    public function __construct(Member $member, Amount $amount)
    {
        $this->member = $member;
        $this->amount = $amount;
    }

    public function isCreditor(): bool
    {
        return $this->amount->toCents() > 0;
    }

    public function isDebitor(): bool
    {
        return !$this->isCreditor();
    }

    public function abs(): Amount
    {
        return new Amount(abs($this->amount->toCents()));
    }

    public function add(Amount $amount): Balance
    {
        return new Balance(
            $this->member,
            $this->amount->add($amount)
        );
    }

    public function subtract(Amount $amount): Balance
    {
        return new Balance(
            $this->member,
            $this->amount->subtract($amount)
        );
    }
}
