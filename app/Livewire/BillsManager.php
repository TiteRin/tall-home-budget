<?php

namespace App\Livewire;

use App\Services\BillService;
use Illuminate\View\View;
use Livewire\Component;

class BillsManager extends Component
{
    protected BillService $billService;

    public function mount(BillService $billService): void
    {
        $this->billService = $billService;
    }

    public function render(): View
    {
        $bills = $this->billService->getBillsCollection();

        return view(
            'livewire.bills-manager',
            compact('bills')
        );
    }
}
