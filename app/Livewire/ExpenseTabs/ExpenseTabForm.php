<?php

namespace App\Livewire\ExpenseTabs;

use App\Actions\ExpenseTab\CreateExpenseTab;
use App\Actions\ExpenseTab\UpdateExpenseTab;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use Exception;
use Illuminate\View\View;
use Livewire\Component;

class ExpenseTabForm extends Component
{
    private ?Household $household = null;

    public ?int $currentExpenseTabId = null;

    public string $newName = '';
    public int $newStartDay = 1;
    private $currentExpenseTab;

    public function boot(CurrentHouseholdServiceContract $householdService)
    {
        $this->household = $householdService->getCurrentHousehold();
    }

    public function mount()
    {
        if ($this->currentExpenseTabId !== null) {
            $this->currentExpenseTab = ExpenseTab::find($this->currentExpenseTabId);
            $this->newName = $this->currentExpenseTab->name;
            $this->newStartDay = $this->currentExpenseTab->from_day;
        }
    }

    public function submitForm()
    {

        if ($this->household === null) {
            throw new Exception();
        }

        $this->validate();

        if ($this->currentExpenseTabId !== null) {
            $this->saveExpenseTab(new UpdateExpenseTab());
        } else {
            $this->createExpenseTab(new CreateExpenseTab());
            $this->reset(['newName', 'newStartDay']);
        }

    }

    public function createExpenseTab(CreateExpenseTab $createExpenseTab): void
    {
        try {
            $expenseTab = $createExpenseTab->handle(
                $this->household->id,
                $this->newName,
                $this->newStartDay
            );
            $this->reset(['newName', 'newStartDay']);
            $this->dispatch('refresh-expense-tabs', $expenseTab);
        } catch (Exception $e) {
            dump($e);
        }
    }

    public function saveExpenseTab(UpdateExpenseTab $updateExpenseTab): void
    {
        try {
            $expenseTab = $updateExpenseTab->handle(
                $this->currentExpenseTabId,
                [
                    'name' => $this->newName,
                    'from_day' => $this->newStartDay
                ]
            );

            $this->dispatch('refresh-expense-tabs', $expenseTab);
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
