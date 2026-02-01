<?php

namespace App\Livewire\ExpenseTabs;

use Illuminate\View\View;
use Livewire\Component;

class ExpenseTabForm extends Component
{

    public string $newName = '';
    public int $newStartDay = 1;

    public function saveExpenseTab()
    {
        $this->validate();
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
