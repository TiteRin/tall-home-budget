<?php

namespace App\Livewire\ExpenseTabs;

use App\Domains\ValueObjects\Amount;
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

    #[On('refresh-expenses-table')]
    public function refreshExpenses()
    {
        $this->resetPage();
    }

    public function updatedPaginators($page, $pageName)
    {
        $this->dispatch('expenses-table-page-updated', page: $page, tabId: $this->expenseTabId);
    }

    public function render()
    {
        $expenseTab = ExpenseTab::find($this->expenseTabId);
        $expenses = Expense::where('expense_tab_id', $this->expenseTabId)
            ->with('member')
            ->orderBy('spent_on', 'desc')
            ->paginate(15);

        $expenseSolver = new ExpenseTabResolver($expenseTab->from_day);
        $monthyPeriod = $expenseSolver->getCurrentMonthlyPeriod();

        $currentPeriodStart = $monthyPeriod->getFrom();
        $currentPeriodEnd = $monthyPeriod->getTo();

        $totalAmount = Expense::where('expense_tab_id', $this->expenseTabId)
            ->where('spent_on', '>=', $currentPeriodStart)
            ->where('spent_on', '<=', $currentPeriodEnd)
            ->get()
            ->reduce(fn($carry, $expense) => $carry->add($expense->amount), new Amount(0));

        return view('livewire.expense-tabs.expenses-table', compact('expenses', 'currentPeriodStart', 'currentPeriodEnd', 'totalAmount'));
    }
}
