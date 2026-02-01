<?php

namespace App\Livewire\ExpenseTabs;

use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ExpenseTabsList extends Component
{

    private Household $household;
    private Collection $expensesTabs;

    public function mount(CurrentHouseholdServiceContract $householdService): void
    {
        $this->household = $householdService->getCurrentHousehold();
        $this->expensesTabs = $this->household->expenseTabs;
    }

    public function render(): View
    {
        $expenseTabs = $this->expensesTabs;
        return view('livewire.expense-tabs.list', compact('expenseTabs'));
    }
}
