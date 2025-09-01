<?php

namespace App\Services\Bill;

use App\Models\Household;
use App\Presenters\BillsOverviewPresenter;
use App\Repositories\Contracts\BillRepository;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Support\Collection;

readonly class BillService
{
    public function __construct(
        protected HouseholdServiceContract $householdService,
        protected BillRepository         $billRepository,
        protected BillsOverviewPresenter $presenter
    ) {}


    public function getBillsForHousehold(int $householdId = null): array
    {
        $household = $this->getHousehold($householdId);

        if (!$household) {
            return BillsOverviewPresenter::empty();
        }

        $bills = $this->billRepository->listForHousehold($household->id);
        $household->setRelation('bills', $bills);

        return $this->presenter->present($household, $bills);
    }

    public function getBillsCollection(int $householdId = null): Collection
    {
        $household = $this->getHousehold($householdId);

        if (!$household) {
            return collect();
        }

        return $this->billRepository->listForHousehold($household->id);
    }

    private function getHousehold(int $householdId = null): ?Household
    {
        if ($householdId) {
            return $this->householdService->getHousehold($householdId);
        }

        return $this->householdService->getCurrentHousehold();
    }
}
