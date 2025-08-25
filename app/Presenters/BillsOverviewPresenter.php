<?php

namespace App\Presenters;

use App\Http\Resources\BillResource;
use App\Http\Resources\BillResourceCollection;
use App\Models\Household;
use App\Services\Household\HouseholdSummaryService;
use Illuminate\Support\Collection;

class BillsOverviewPresenter
{

    public function __construct(private HouseholdSummaryService $householdSummaryService)
    {
    }

    public function present(Household $household, Collection $bills): array
    {

        $billCollection = new BillResourceCollection(BillResource::collection($bills));

        return [
            'bills' => $billCollection,
            'household_summary' => $this->householdSummaryService->forHousehold($household),
        ];
    }

    public static function empty(): array
    {
        return [
            'bills' => new BillResourceCollection(collect()),
            'household_summary' => null,
        ];
    }
}
