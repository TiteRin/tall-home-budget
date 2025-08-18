<?php

namespace App\Livewire\Bills;

use App\Models\Bill;
use Illuminate\View\View;
use Livewire\Component;

class Row extends Component
{
    #[Prop]
    public Bill $bill;

    public function mount(Bill $bill): void
    {
        $this->bill = $bill;
    }

    public function render(): View
    {
        return view('livewire.bills.row');
    }

    public function edit(): void
    {
        $this->dispatch('editBill', billId: $this->bill->id);
    }

    public function delete(): void
    {
        $this->bill->delete();
        $this->dispatch('refreshBills');
    }
}
