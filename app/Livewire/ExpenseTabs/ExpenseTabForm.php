<?php

namespace App\Livewire\ExpenseTabs;

use Illuminate\View\View;
use Livewire\Component;

class ExpenseTabForm extends Component
{
    public function render(): View
    {
        return view('livewire.expense-tabs.form');
    }
}
