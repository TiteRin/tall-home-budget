<?php

namespace App\Actions\Expenses;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Models\Member;
use App\Services\Household\CurrentHouseholdServiceContract;
use Carbon\CarbonImmutable;

class CreateExpense
{
    private Household $currentHousehold;

    public function __construct(CurrentHouseholdServiceContract $householdService)
    {
        $this->currentHousehold = $householdService->getCurrentHousehold();
    }

    public function handle(
        int                $memberId,
        DistributionMethod $distributionMethod,
        int                $expenseTabId,
        string             $name,
        CarbonImmutable    $spentOn,
        Amount             $amount,
    )
    {

        $member = Member::findOrFail($memberId);
        $expenseTab = ExpenseTab::findOrFail($expenseTabId);

        if (!$member || !$expenseTab) {
            throw new \InvalidArgumentException('Member or expense tab not found');
        }

        if ($member->household_id !== $this->currentHousehold->id ||
            $expenseTab->household_id !== $this->currentHousehold->id) {
            throw new MismatchedHouseholdException();
        }

        return Expense::create([
            'name' => $name,
            'amount' => $amount->toCents(),
            'spent_on' => $spentOn,
            'member_id' => $memberId,
            'expense_tab_id' => $expenseTabId,
            'distribution_method' => $distributionMethod->value
        ]);
    }

}
