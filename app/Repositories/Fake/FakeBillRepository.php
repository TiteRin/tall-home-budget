<?php

namespace App\Repositories\Fake;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Repositories\Contracts\BillRepository;
use Illuminate\Support\Collection;

class FakeBillRepository implements BillRepository
{
    /**
     * Collection to store created bills
     */
    private Collection $bills;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bills = collect();
    }

    /**
     * Create a new bill
     *
     * @param string $name
     * @param Amount $amount
     * @param DistributionMethod $distributionMethod
     * @param int $householdId
     * @param int|null $memberId
     * @return Bill
     */
    public function create(
        string             $name,
        Amount             $amount,
        DistributionMethod $distributionMethod,
        int                $householdId,
        ?int               $memberId = null
    ): Bill
    {
        // Create a bill instance without saving to database
        $bill = new Bill();
        $bill->name = $name;
        $bill->amount = $amount;
        $bill->distribution_method = $distributionMethod;
        $bill->household_id = $householdId;
        $bill->member_id = $memberId;

        // Add to our collection
        $this->bills->push($bill);

        return $bill;
    }

    public function find(int $billId): ?Bill
    {
        return $this->bills->firstWhere('id', $billId);
    }

    public function listForHousehold(int $householdId): Collection
    {
        return $this->bills->where('household_id', $householdId)->values();
    }

    public function listForMember(int $memberId): Collection
    {
        return $this->bills->where('member_id', $memberId)->values();
    }

    /**
     * Get all bills created by this repository
     *
     * @return Collection
     */
    public function getCreatedBills(): Collection
    {
        return $this->bills;
    }

    /**
     * Get the last created bill
     *
     * @return Bill|null
     */
    public function getLastCreatedBill(): ?Bill
    {
        return $this->bills->last();
    }
}
