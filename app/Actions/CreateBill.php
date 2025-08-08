<?php

namespace App\Actions;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Repositories\BillRepository;

readonly class CreateBill
{
    public function __construct(
        private BillRepository $billRepository,
    )
    {
    }

    public function handle(
        string             $billName,
        Amount             $amount,
        DistributionMethod $distributionMethod,
        int                $householdId,
        ?int               $memberId = null
    ): Bill
    {
        return $this->billRepository->create(
            $billName,
            $amount,
            $distributionMethod,
            $householdId,
            $memberId
        );
    }
}
