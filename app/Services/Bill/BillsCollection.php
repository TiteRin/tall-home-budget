<?php

namespace App\Services\Bill;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Member;
use Exception;
use Illuminate\Support\Collection;

class BillsCollection
{
    private Collection $bills;

    public function __construct($bills = [])
    {
        $this->bills = collect($bills);
    }

    public function toArray(): array
    {
        return $this->bills->toArray();
    }

    public function getTotal(): Amount
    {
        if ($this->bills->isEmpty()) {
            return new Amount(0);
        }

        return new Amount($this->bills->sum(fn($bill) => $bill->amount->value()));
    }

    public function getTotalForDistributionMethod(DistributionMethod $distributionMethod): Amount
    {
        return (new BillsCollection($this->bills->filter(fn($bill) => $bill->distribution_method === $distributionMethod)))->getTotal();
    }

    public function getTotalForMember(?Member $member = null): Amount
    {
        return (new BillsCollection($this->bills->filter(fn($bill) => $bill->member_id === $member?->id)))->getTotal();
    }

    /**
     * @throws Exception
     */
    public function add(Bill $bill): void
    {
        if ($this->bills->contains('id', $bill->id)) {
            throw new Exception('Bill already exists');
        }
        $this->bills->push($bill);
    }
}
