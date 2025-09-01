<?php

namespace App\Actions\Bills;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Repositories\Contracts\BillRepository;
use App\Services\Household\HouseholdServiceContract;
use Exception;

class CreateBill
{
    public function __construct(
        private BillRepository $billRepository,
        private HouseholdServiceContract $householdService
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(
        string             $billName,
        Amount             $amount,
        DistributionMethod $distributionMethod,
        ?int               $memberId = null
    ): Bill
    {
        $household = $this->householdService->getCurrentHousehold();

        if (!$household) {
            throw new Exception('No current household found');
        }

        return $this->billRepository->create(
            $billName,
            $amount,
            $distributionMethod,
            $household->id,
            $memberId
        );
    }
}
