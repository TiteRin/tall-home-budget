<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Services\Bill\BillService;
use App\Services\Household\HouseholdService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class BillsManager extends Component
{
    protected HouseholdService $householdService;
    protected BillService $billService;

    protected Collection $bills;

    public string $newName = '';
    public float $newAmount = 0;
    public DistributionMethod $newDistributionMethod = DistributionMethod::EQUAL;
    public int|null $newMemberId = null;

    public function boot(HouseholdService $householdService, BillService $billService): void
    {
        $this->householdService = $householdService;
        $this->billService = $billService;
    }

    public function render(): View
    {
        // Only refresh bills if they haven't been loaded yet
        if (!isset($this->bills)) {
            $this->bills = $this->billService->getBillsCollection();
        }

        $bills = $this->bills;

        return view(
            'livewire.bills.manager',
            compact(
                'bills',
            )
        );
    }

    public function getHouseholdMembersProperty(): Collection
    {
        $household = $this->householdService->getCurrentHousehold();

        if ($household === null) {
            return collect();
        }

        return $household->members;
    }

    public function getDefaultDistributionMethodProperty(): DistributionMethod
    {
        return $this->householdService->getCurrentHousehold()?->getDefaultDistributionMethod() ?? DistributionMethod::EQUAL;
    }

    public function getHasHouseholdJointAccountProperty(): bool
    {
        return $this->householdService->getCurrentHousehold()->hasJointAccount();
    }

    /**
     * Refresh the bills collection
     * This method is called when the 'refreshBills' event is dispatched
     */
    #[On('refreshBills')]
    public function refreshBills(): void
    {
        $this->bills = $this->billService->getBillsCollection();

        $this->dispatch('notify', [
            'message' => 'Bills refreshed successfully',
            'type' => 'success'
        ]);
    }
}
