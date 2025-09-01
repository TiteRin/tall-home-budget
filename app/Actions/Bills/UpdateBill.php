<?php

namespace App\Actions\Bills;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Repositories\BillRepository;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateBill
{
    public function __construct(
        private readonly BillRepository           $billRepository,
        private readonly HouseholdServiceContract $householdService
    )
    {
    }

    public function handle(
        int                $billId,
        string             $billName,
        Amount             $amount,
        DistributionMethod $distributionMethod,
        ?int               $memberId = null
    ): void
    {

        $currentHousehold = $this->householdService->getCurrentHousehold();

        throw new ModelNotFoundException();
    }
}
