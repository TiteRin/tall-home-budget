<?php

namespace App\Livewire\ExpenseTabs;

use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ExpenseTabsList extends Component
{

    public int $currentHouseholdId;

    #[Url(as: 'tab')]
    public string $activeTab = 'new';

    public function mount(CurrentHouseholdServiceContract $householdService): void
    {
        $this->currentHouseholdId = $householdService->getCurrentHousehold()->id;
    }

    #[On('refresh-expense-tabs')]
    public function onRefreshExpenseTab($expenseTab)
    {
        unset($this->expensesTabs);
    }

    #[Computed]
    public function expensesTabs(): Collection
    {
        $household = Household::query()->findOrFail($this->currentHouseholdId);
        return $household->expenseTabs;
    }

    public function render(): View
    {
        return view('livewire.expense-tabs.list');
    }
}
