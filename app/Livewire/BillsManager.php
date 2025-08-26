<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Services\Bill\BillService;
use App\Services\Household\HouseholdService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class BillsManager extends Component
{
    protected HouseholdService $householdService;
    protected BillService $billService;

    public Collection $bills;

    public bool $isEditing = false;
    public ?int $editingBillId = null;

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

        return view('livewire.bills.manager', compact('bills'));
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

    #[On('billDeleted')]
    public function removeBill(int $billId): void
    {
        $bill = Bill::find($billId);
        if (!$bill) {
            throw new ModelNotFoundException();
        }
        $bill->delete();
        $this->refreshBills();
    }

    #[On('editBill')]
    public function editBill(int $billId): void
    {
        $this->isEditing = true;
        $this->editingBillId = $billId;
    }

    #[On('cancelEditBill')]
    public function cancelEditBill(): void
    {
        $this->isEditing = false;
        $this->editingBillId = null;
    }

    #[On('billHasBeenUpdated')]
    public function saveBill(): void
    {
        $this->isEditing = false;
        $this->editingBillId = null;
        $this->refreshBills();
    }
}
