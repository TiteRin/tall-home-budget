<?php

namespace App\Livewire\ExpenseTabs;

use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ExpenseTabsList extends Component
{

    public int $currentHouseholdId;

    private Collection $expensesTabs;

    public function mount(CurrentHouseholdServiceContract $householdService): void
    {
        $this->currentHouseholdId = $householdService->getCurrentHousehold()->id;
        $this->expensesTabs = collect();

        $this->refreshList();;
    }

    #[On('refresh-expense-tabs')]
    public function onRefreshExpenseTab($expenseTab)
    {
        $this->refreshList();
    }

    public function render(): View
    {
        $expenseTabs = $this->expensesTabs;
        return view('livewire.expense-tabs.list', compact('expenseTabs'));
    }

    private function refreshList(): void
    {
        $household = Household::query()->findOrFail($this->currentHouseholdId);
        $household->refresh();
        $this->expensesTabs = $household->expenseTabs;
    }
}
