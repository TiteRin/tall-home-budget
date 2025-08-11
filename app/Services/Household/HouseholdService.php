<?php

namespace App\Services\Household;

use App\Http\Resources\HouseholdSummaryResource;
use App\Models\Household;

readonly class HouseholdService
{
    public function getHousehold(int $id): Household|null
    {
        return Household::find($id);
    }

    public function getCurrentHousehold(): Household|null
    {
        return Household::orderBy('id')->first();
    }

    public function getSummary(int $householdId): array|null
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
