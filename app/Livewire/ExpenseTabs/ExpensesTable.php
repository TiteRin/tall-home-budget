<?php

namespace App\Livewire\ExpenseTabs;

use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Services\Expense\ExpenseTabResolver;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ExpensesTable extends Component
{
    use WithPagination, WithoutUrlPagination;

    public int $expenseTabId;
    public ?int $editingExpenseId = null;

    #[On('refresh-expense-tabs')]
    public function onRefreshExpenseTab($expenseTab)
    {
        if ($expenseTab['id'] == $this->expenseTabId) {
            $this->resetPage();
        }
    }

    #[On('refresh-expenses-table')]
    public function refreshExpenses()
    {
        $this->resetPage();
    }

    public function updatedPaginators($page, $pageName)
    {
        $this->dispatch('expenses-table-page-updated', page: $page, tabId: $this->expenseTabId);
    }

    public function editExpense(int $expenseId)
    {
        $this->editingExpenseId = $expenseId;
    }

    #[On('expense-has-been-updated')]
    #[On('cancel-edit-expense')]
    public function stopEditing()
    {
        $this->editingExpenseId = null;
    }

    public function render()
    {
        $expenseTab = ExpenseTab::find($this->expenseTabId);
        $expenses = Expense::where('expense_tab_id', $this->expenseTabId)
            ->with('member')
            ->orderBy('spent_on', 'desc')
            ->paginate(15);

        $expenseTabResolver = new ExpenseTabResolver($expenseTab);
        $monthlyPeriod = $expenseTabResolver->getCurrentMonthlyPeriod();

        $currentPeriodStart = $monthlyPeriod->getFrom();
        $currentPeriodEnd = $monthlyPeriod->getTo();
        $totalAmount = $expenseTabResolver->getTotalAmountFor($monthlyPeriod);


        return view('livewire.expense-tabs.expenses-table', compact('expenses', 'currentPeriodStart', 'currentPeriodEnd', 'totalAmount'));
    }
}
