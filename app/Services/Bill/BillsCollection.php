<?php

namespace App\Services\Bill;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Member;
use App\Support\Collections\TypedCollection;
use Exception;
use InvalidArgumentException;

class BillsCollection extends TypedCollection
{

    public function getTotal(): Amount
    {
        if ($this->isEmpty()) {
            return new Amount(0);
        }

        return new Amount($this->sum(fn($bill) => $bill->amount->value()));
    }

    public function getTotalForDistributionMethod(DistributionMethod $distributionMethod): Amount
    {
        $filtered = $this->filter(function (Bill $bill) use ($distributionMethod) {
            return $bill->distribution_method === $distributionMethod;
        });

        return $filtered->getTotal();
    }

    public function getTotalForMember(?Member $member = null): Amount
    {
        return $this->filter(fn($bill) => $bill->member_id === $member?->id)->getTotal();
    }

    /**
     * @throws Exception
     */
    protected function validateType($item): void
    {
        parent::validateType($item);

        if ($this->contains('id', $item->id)) {
            throw new InvalidArgumentException('Bill already exists');
        }
    }

    protected function getExpectedType(): string
    {
        return Bill::class;
    }

    protected function getCollectionName(): string
    {
        return self::class;
    }
}
