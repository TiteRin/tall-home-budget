<?php

namespace App\Repositories\Contracts;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use Illuminate\Support\Collection;

interface BillRepository
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
    ): Bill;

    public function find(int $billId): ?Bill;

    public function listForHousehold(int $householdId): Collection;

    public function listForMember(int $memberId): Collection;
}
