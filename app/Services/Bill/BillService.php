<?php

namespace App\Services\Bill;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Http\Resources\BillResource;
use App\Http\Resources\BillResourceCollection;
use App\Http\Resources\HouseholdSummaryResource;
use App\Models\Bill;
use App\Models\Household;
use App\Repositories\BillRepository;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Support\Collection;

readonly class BillService
{
    public function __construct(
        protected HouseholdServiceContract $householdService,
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

        $bills = $household->bills()->with('member')->get();
        $billCollection = new BillResourceCollection(BillResource::collection($bills));

        return [
            'bills' => $billCollection,
            'household_summary' => new HouseholdSummaryResource($household),
        ];
    }

    public function getBillsCollection(int $householdId = null): Collection
    {
        $household = $this->getHousehold($householdId);

        if (!$household) {
            return collect();
        }

        return $household->bills()->with('member')->get();
    }

    public function getHouseholdSummary(int $householdId = null): ?array
    {
        return $this->householdService->getSummary($householdId);
    }

    private function getHousehold(int $householdId = null): ?Household
    {
        if ($householdId) {
            return $this->householdService->getHousehold($householdId);
        }

        return $this->householdService->getCurrentHousehold();
    }

    /**
     * Create a new bill
     *
     * @param string $name
     * @param Amount $amount
     * @param DistributionMethod $distributionMethod
     * @param int|null $householdId
     * @param int|null $memberId
     * @return Bill
     */
    public function createBill(
        string             $name,
        Amount             $amount,
        DistributionMethod $distributionMethod,
        ?int               $householdId = null,
        ?int               $memberId = null
    ): Bill
    {
        $household = $this->getHousehold($householdId);

        if (!$household) {
            throw new \InvalidArgumentException('Household not found');
        }

        return $this->billRepository->create(
            $name,
            $amount,
            $distributionMethod,
            $household->id,
            $memberId
        );
    }
}
