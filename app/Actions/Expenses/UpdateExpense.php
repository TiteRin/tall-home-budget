<?php

namespace App\Actions\Expenses;

use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Models\Member;
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

        $memberId = $data['member_id'] ?? $expense->member_id;
        $expenseTabId = $data['expense_tab_id'] ?? $expense->expense_tab_id;
        $member = Member::findOrFail($memberId);
        $expenseTab = ExpenseTab::findOrFail($expenseTabId);

        if (!$expense) {
            throw new ModelNotFoundException();
        }

        if ($this->currentHousehold->id !== $member->household_id ||
            $this->currentHousehold->id !== $expenseTab->household_id) {
            throw new MismatchedHouseholdException();
        }


        $expense->fill($data);
        if ($expense->isDirty()) {
            $expense->save();
        }

        return $expense;
    }

}
