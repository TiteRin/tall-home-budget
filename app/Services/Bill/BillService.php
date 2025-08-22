<?php

namespace App\Services\Bill;

use App\Http\Resources\BillResource;
use App\Http\Resources\BillResourceCollection;
use App\Models\Household;
use App\Repositories\BillRepository;
use App\Services\Household\HouseholdServiceContract;
use App\Services\Household\HouseholdSummaryService;
use Illuminate\Support\Collection;

readonly class BillService
{
    public function __construct(
        protected HouseholdServiceContract $householdService,
        protected HouseholdSummaryService $householdSummaryService,
        protected BillRepository   $billRepository
    ) {}


    public function getBillsForHousehold(int $householdId = null): array
    {
        $household = $this->getHousehold($householdId);

        if (!$household) {
            return [
                'bills' => new BillResourceCollection(collect()),
                'household_summary' => null,
            ];
        }

        $bills = $this->billRepository->listForHousehold($household->id);
        $household->setRelation('bills', $bills);
        $billCollection = new BillResourceCollection(BillResource::collection($bills));

        return [
            'bills' => $billCollection,
            'household_summary' => $this->householdSummaryService->forHousehold($household),
        ];
    }

    public function getBillsCollection(int $householdId = null): Collection
    {
        $household = $this->getHousehold($householdId);

        if (!$household) {
            return collect();
        }

        return $this->billRepository->listForHousehold($household->id);
    }

    public function getHouseholdSummary(int $householdId = null): ?array
    {
        return $this->householdSummaryService->getSummaryArray($householdId);
    }

    private function getHousehold(int $householdId = null): ?Household
    {
        if ($householdId) {
            return $this->householdService->getHousehold($householdId);
        }

        return $this->householdService->getCurrentHousehold();
    }
}
