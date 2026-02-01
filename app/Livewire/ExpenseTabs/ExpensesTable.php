<?php

namespace App\Livewire\ExpenseTabs;

use App\Models\Expense;
use Illuminate\Support\Collection;
use Livewire\Component;

class ExpensesTable extends Component
{
    public int $expenseTabId;
    private Collection $expenses;

    public function boot()
    {
        $this->expenses = Expense::where('expense_tab_id', $this->expenseTabId)
            ->with('member')
            ->get();
    }

    public function render()
    {
        $expenses = $this->expenses;
        return view('livewire.expense-tabs.expenses-table', compact('expenses'));
    }
}
