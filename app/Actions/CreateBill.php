<?php

namespace App\Actions;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Repositories\BillRepository;
use App\Services\Household\HouseholdServiceContract;

class CreateBill
{
    public function __construct(
        private BillRepository $billRepository,
        private HouseholdServiceContract $householdService
    )
    {
    }

    public function handle(
        string             $billName,
        Amount             $amount,
        DistributionMethod $distributionMethod,
        ?int               $memberId = null
    ): Bill
    {
        $household = $this->householdService->getCurrentHousehold();

        return $this->billRepository->create(
            $billName,
            $amount,
            $distributionMethod,
            $household->id,
            $memberId
        );
    }
}
