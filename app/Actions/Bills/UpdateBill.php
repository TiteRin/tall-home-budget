<?php

namespace App\Actions\Bills;

use App\Domains\ValueObjects\Amount;
use App\Exceptions\Households\InvalidHouseholdException;
use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\Bill;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateBill
{
    public function __construct(
        private readonly CurrentHouseholdServiceContract $householdService
    )
    {
    }

    /**
     * @throws InvalidHouseholdException
     * @throws MismatchedHouseholdException
     */
    public function handle(
        int                $billId,
        array $data,
    ): Bill
    {

        $currentHousehold = $this->householdService->getCurrentHousehold();

        $existingBill = Bill::findOrFail($billId);

        if ($existingBill->household_id !== $currentHousehold->id) {
            throw new ModelNotFoundException();
        }

        if (isset($data['amount'])) {
            if (is_string($data['amount'])) {
                $data['amount'] = Amount::from($data['amount']);
            }
        }

        if (array_key_exists('member_id', $data)) {
            $memberId = $data['member_id'];

            if ($memberId === null and !$currentHousehold->hasJointAccount()) {
                throw new InvalidHouseholdException();
            }

            if ($memberId !== null && !$currentHousehold->members()->where('id', $memberId)->exists()) {
                throw new MismatchedHouseholdException();
            }
        }

        if (array_key_exists('household_id', $data)) {
            throw new ModelNotFoundException();
        }

        $existingBill->fill($data);

        if ($existingBill->isDirty()) {
            $existingBill->save();
        }

        return $existingBill;
    }
}
