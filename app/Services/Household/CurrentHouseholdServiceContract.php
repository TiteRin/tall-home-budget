<?php

namespace App\Services\Household;

use App\Models\Household;

interface CurrentHouseholdServiceContract
{
    public function getCurrentHousehold(): ?Household;
}
