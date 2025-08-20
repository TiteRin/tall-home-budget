<?php

namespace App\Services\Household;

use App\Models\Household;

interface HouseholdServiceContract
{

    public function getHousehold(int $householdId): ?Household;

    public function getCurrentHousehold(): ?Household;

    public function getSummary(?int $householdId = null): ?array;

}
