<?php

namespace Tests\Unit\Services\Bill;

use App\Presenters\BillsOverviewPresenter;
use App\Services\Bill\BillService;
use App\Services\Household\CurrentHouseholdServiceContract;
use Mockery as m;


test("when household is null, should return an array with no bills", function () {

    $householdService = m::mock(CurrentHouseholdServiceContract::class);
    $presenter = m::mock(BillsOverviewPresenter::class);
    $billService = new BillService(
        $householdService,
        $presenter
    );

    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);

    $array = $billService->getBillsForHousehold(null);

    expect($array)
        ->toBeArray()
        ->toHaveKeys(['bills', 'household_summary'])
        ->and($array['bills'])->toBeEmpty()
        ->and($array['household_summary'])->toBeNull();
});
