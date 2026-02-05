<?php

namespace App\Actions\ExpenseTab;

use App\Models\ExpenseTab;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateExpenseTab
{
    public function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public function handle(
        int   $id,
        array $data
    ): ExpenseTab
    {
        $editingExpenseTab = ExpenseTab::findOrFail($id);

        if ($editingExpenseTab === null) {
            throw new ModelNotFoundException('Expense tab not found');
        }

        $editingExpenseTab->update($data);
        return $editingExpenseTab;
    }
}
