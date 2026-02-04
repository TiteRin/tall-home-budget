<?php

namespace App\Actions\Expenses;

use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\Expense;
use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;

class DeleteExpense
{
    private Household $currentHousehold;

    public function __construct(CurrentHouseholdServiceContract $householdService)
    {
        $this->currentHousehold = $householdService->getCurrentHousehold();
    }

    public function handle(int $expenseId): void
    {
        $expense = Expense::findOrFail($expenseId);

        if ($expense->expenseTab->household_id !== $this->currentHousehold->id) {
            throw new MismatchedHouseholdException();
        }

        $expense->delete();
    }
}
