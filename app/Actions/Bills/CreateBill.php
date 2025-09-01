<?php

namespace App\Actions\Bills;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Services\Household\HouseholdServiceContract;
use Exception;

class CreateBill
{
    public function __construct(
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

        return Bill::create([
            'name' => $billName,
            'amount' => $amount,
            'distribution_method' => $distributionMethod,
            'household_id' => $household->id,
            'member_id' => $memberId,
        ]);
    }
}
