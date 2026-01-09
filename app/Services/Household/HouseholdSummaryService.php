<?php

namespace App\Services\Household;

use App\Http\Resources\HouseholdSummaryResource;
use App\Models\Household;

readonly class HouseholdSummaryService
{
    public function __construct(private CurrentHouseholdServiceContract $householdService)
    {
    }

    public function forHousehold(Household $household): ?HouseholdSummaryResource
    {
        return new HouseholdSummaryResource($household);
    }

    public function getSummary(?int $householdId = null): ?HouseholdSummaryResource
    {
        $household = $householdId
            ? $this->householdService->getHousehold($householdId)
            : $this->householdService->getCurrentHousehold();

        return $household ? $this->forHousehold($household) : null;
    }

    public function getSummaryArray(?int $householdId = null): ?array
    {
        $ressource = $this->getSummary($householdId);
        return $ressource?->toArray(request());
    }
}
