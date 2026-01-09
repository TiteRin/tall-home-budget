<?php

namespace App\Services\Household;

use App\Models\Household;
use Illuminate\Support\Facades\Auth;

class CurrentHouseholdService implements CurrentHouseholdServiceContract
{
    /**
     * @deprecated
     */
    public function getHousehold(int $householdId): Household|null
    {
        return Household::find($householdId);
    }

    public function getCurrentHousehold(): Household|null
    {
        if (!Auth::check()) {
            return Household::orderBy('created_at')->first();
        }
        $user = Auth::user();
        return Household::where('id', $user->member()->first()->household_id)->first();
    }
}
