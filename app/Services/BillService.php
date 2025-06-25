<?php

namespace App\Services;

use App\Models\Household;
use App\Models\Bill;
use App\Http\Resources\BillResource;
use App\Http\Resources\BillCollection;
use App\Http\Resources\HouseholdSummaryResource;
use Illuminate\Support\Collection;

class BillService
{
    public function getBillsForHousehold(int $householdId = null): array
    {
        $household = $this->getHousehold($householdId);
        
        if (!$household) {
            return [
                'bills' => new BillCollection(collect([])),
                'household_summary' => null,
            ];
        }

        $bills = $household->bills()->with('member')->get();
        $billCollection = new BillCollection(BillResource::collection($bills));

        return [
            'bills' => $billCollection,
            'household_summary' => new HouseholdSummaryResource($household),
        ];
    }

    public function getBillsCollection(int $householdId = null): Collection
    {
        $household = $this->getHousehold($householdId);
        
        if (!$household) {
            return collect([]);
        }

        return $household->bills()->with('member')->get();
    }

    public function getHouseholdSummary(int $householdId = null): ?array
    {
        $household = $this->getHousehold($householdId);
        
        if (!$household) {
            return null;
        }

        return (new HouseholdSummaryResource($household))->toArray(request());
    }

    private function getHousehold(int $householdId = null): ?Household
    {
        if ($householdId) {
            return Household::find($householdId);
        }

        return Household::orderBy('created_at')->first();
    }
} 