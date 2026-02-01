<?php

namespace App\Livewire\ExpenseTabs;

use App\Actions\ExpenseTab\CreateExpenseTab;
use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use Exception;
use Illuminate\View\View;
use Livewire\Component;

class ExpenseTabForm extends Component
{
    private ?Household $household = null;

    public string $newName = '';
    public int $newStartDay = 1;

    public function boot(CurrentHouseholdServiceContract $householdService)
    {
        $this->household = $householdService->getCurrentHousehold();
    }

    public function saveExpenseTab(CreateExpenseTab $createExpenseTab): void
    {
        if ($this->household === null) {
            throw new Exception();
        }


        $this->validate();

        try {
            $expenseTab = $createExpenseTab->handle(
                $this->household->id,
                $this->newName,
                $this->newStartDay
            );
            $this->reset(['newName', 'newStartDay']);
            $this->dispatch('expenseTabCreated', $expenseTab);
        } catch (Exception $e) {
            dump($e);
        }
    }

    public function rules()
    {
        return [
            'newName' => 'required',
            'newStartDay' => 'required|integer|min:1|max:31'
        ];
    }

    public function render(): View
    {
        return view('livewire.expense-tabs.form');
    }
}
