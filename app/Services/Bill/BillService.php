<?php

namespace App\Services\Bill;

use App\Models\Bill as BillModel;
use App\Models\Household;
use App\Presenters\BillsOverviewPresenter;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Support\Collection;

readonly class BillService
{
    public function __construct(
        protected HouseholdServiceContract $householdService,
        protected BillsOverviewPresenter $presenter
    ) {}


    public function getBillsForHousehold(int $householdId = null): array
    {
        $household = $this->getHousehold($householdId);

        if (!$household) {
            return BillsOverviewPresenter::empty();
        }

        $bills = BillModel::query()
            ->where('household_id', $household->id)
            ->with('member')
            ->get();
        $household->setRelation('bills', $bills);

        return $this->presenter->present($household, $bills);
    }

    public function getBillsCollection(int $householdId = null): Collection
    {
        $household = $this->getHousehold($householdId);

        if (!$household) {
            return collect();
        }

        return BillModel::query()
            ->where('household_id', $household->id)
            ->with('member')
            ->get();
    }

    private function getHousehold(int $householdId = null): ?Household
    {
        if ($householdId) {
            return $this->householdService->getHousehold($householdId);
        }

        return $this->householdService->getCurrentHousehold();
    }
}
