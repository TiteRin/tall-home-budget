<?php

namespace App\Domains\ValueObjects;

use App\Enums\DistributionMethod;
use App\Models\Member;
use App\Support\Collections\TypedCollection;

class ChargesCollection extends TypedCollection
{

    public function getTotalAmount(): Amount
    {
        if ($this->isEmpty()) {
            return new Amount(0);
        }

        return new Amount($this->sum(fn(Charge $charge) => $charge->getAmountOrZero()->value()));
    }

    public function getTotalAmountForMember(Member $member): Amount
    {
        return $this->getTotalAmountForMemberId($member->id);
    }

    public function getTotalAmountForMemberId(int $memberId): Amount
    {
        return $this->filter(function (Charge $charge) use ($memberId) {
            if ($charge->getPayer() === null) {
                return false;
            }

            return $charge->getPayer()->id === $memberId;
        })->getTotalAmount();
    }

    public function getTotalAmountForJointAccount(): Amount
    {
        return $this->filter(fn(Charge $charge) => $charge->getPayer() === null)->getTotalAmount();
    }

    public function getTotalAmountForDistributionMethod(DistributionMethod $distributionMethod): Amount
    {
        $filtered = $this->filter(function (Charge $charge) use ($distributionMethod) {
            return $charge->getDistributionMethod() === $distributionMethod;
        });

        return $filtered->getTotalAmount();
    }

    protected function getExpectedType(): string
    {
        return Charge::class;
    }

    protected function getCollectionName(): string
    {
        return self::class;
    }
}
