<?php

namespace App\Repositories;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;

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
}
