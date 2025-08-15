<?php

namespace App\Livewire\Bills;

use App\Models\Bill;
use Livewire\Component;

class Row extends Component
{
    #[Prop]
    private Bill $bill;

    public function mount(Bill $bill): void
    {
        $this->bill = $bill;
    }

    public function render()
    {
        return view('livewire.bills.row');
    }
}
