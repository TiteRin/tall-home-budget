<?php

namespace App\Services\Household;

use App\Http\Resources\HouseholdSummaryResource;
use App\Models\Household;

class HouseholdService implements HouseholdServiceContract
{
    public function getHousehold(int $householdId): Household|null
    {
        return Household::find($householdId);
    }

    public function getCurrentHousehold(): Household|null
    {
        return Household::orderBy('id')->first();
    }

    public function getSummary(?int $householdId = null): array|null
    {
        $household = $householdId
            ? $this->getHousehold($householdId)
            : $this->getCurrentHousehold();

        if (!$household) {
            return null;
        }

        return (new HouseholdSummaryResource($household))->toArray(request());
    }
}
