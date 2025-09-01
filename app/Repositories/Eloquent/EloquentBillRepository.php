<?php

namespace App\Repositories\Eloquent;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Repositories\Contracts\BillRepository;
use Illuminate\Support\Collection;

class EloquentBillRepository implements BillRepository
{
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
        return Bill::create([
            'name' => $name,
            'amount' => $amount,
            'distribution_method' => $distributionMethod,
            'household_id' => $householdId,
            'member_id' => $memberId,
        ]);
    }

    public function find(int $id): ?Bill
    {
        return Bill::find($id);
    }

    public function listForHousehold(int $householdId): Collection
    {
        return Bill::query()
            ->where('household_id', $householdId)
            ->with('member')
            ->get();
    }

    public function listForMember(int $memberId): Collection
    {
        return Bill::query()
            ->where('member_id', $memberId)
            ->with('household')
            ->get();
    }
}
