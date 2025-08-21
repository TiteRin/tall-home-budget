<?php

namespace App\Services\Household;

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
}
