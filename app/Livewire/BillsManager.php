<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Models\Member;
use App\Services\BillService;
use App\Services\HouseholdService;
use Illuminate\View\View;
use Livewire\Component;

class BillsManager extends Component
{
    protected HouseholdService $householdService;
    protected BillService $billService;

    public string $newName = '';
    public float $newAmount = 0;
    public DistributionMethod $newDistributionMethod = DistributionMethod::EQUAL;
    public int|null $newMemberId = null;

    public function mount(HouseholdService $householdService, BillService $billService): void
    {
        $this->billService = $billService;
        $this->householdService = $householdService;
    }

    public function render(): View
    {
        $bills = $this->billService->getBillsCollection();
        $household = $this->householdService->getCurrentHousehold();

        return view(
            'livewire.bills-manager',
            compact(
                'bills', 'household'
            )
        );
    }

    public function getDistributionMethodsProperty(): array
    {
        return collect(DistributionMethod::cases())->mapWithKeys(
            function(DistributionMethod $method) {
                return [$method->value => $method->label()];
            })->toArray();
    }

    public function getHouseholdMembersProperty(): array
    {
        $household = Household::orderBy('created_at')->first();

        if ($household === null) {
            return [];
        }

        return $household->members->mapWithKeys(
            function(Member $member) {
                return [$member->id => $member->full_name];
            }
        )->toArray();
    }
}
