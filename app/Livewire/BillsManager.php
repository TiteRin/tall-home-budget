<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Services\BillService;
use Illuminate\View\View;
use Livewire\Component;

class BillsManager extends Component
{
    protected BillService $billService;

    public string $newName = '';
    public float $newAmount = 0;
    public DistributionMethod $newDistributionMethod = DistributionMethod::EQUAL;
    public int|null $newMemberId = null;

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

    public function getDistributionMethodsProperty(): array
    {

        return collect(DistributionMethod::cases())->mapWithKeys(function(DistributionMethod $method) {
            return [$method->value => $method->label()];
        })->toArray();
    }
}
