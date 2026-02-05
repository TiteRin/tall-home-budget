<?php

namespace App\Actions\ExpenseTab;

use App\Models\ExpenseTab;
use Exception;

class CreateExpenseTab
{
    public function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public function handle(
        int    $householdId,
        string $expenseTabName,
        int    $startDayOfMonth,
    ): ExpenseTab
    {
        return ExpenseTab::create([
            'name' => $expenseTabName,
            'from_day' => $startDayOfMonth,
            'household_id' => $householdId
        ]);
    }
}
