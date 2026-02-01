<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateExpense
{
    private Household $currentHousehold;

    public function __construct(CurrentHouseholdServiceContract $householdService)
    {
        $this->currentHousehold = $householdService->getCurrentHousehold();
    }

    public function handle(
        int   $expenseId,
        array $data
    )
    {
        $expense = Expense::findOrFail($expenseId);

        if (!$expense) {
            throw new ModelNotFoundException();
        }

        $expense->fill($data);
        if ($expense->isDirty()) {
            $expense->save();
        }

        return $expense;
    }

}
