<?php

namespace Tests\Unit\Services\Household;

use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Services\Household\HouseholdServiceContract;
use App\Services\Household\HouseholdSummaryService;
use Mockery as m;

afterEach(function () {
    m::close();
});

test('should return null if no household is provided', function () {
    $mockHouseholdService = m::mock(HouseholdServiceContract::class);
    $mockHouseholdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);

    $summary = new HouseholdSummaryService($mockHouseholdService);
    expect($summary->getSummaryArray())->toBeNull();
});

test('should return a household summary ressource when forHousehold is called', function () {
    $mockHouseholdService = m::mock(HouseholdServiceContract::class);
    $summary = new HouseholdSummaryService($mockHouseholdService);

    $household = new Household();
    $household->setAttribute('id', 1234);
    $household->setAttribute('name', 'Test Household');
    $household->setAttribute('default_distribution_method', DistributionMethod::EQUAL);

    $resource = $summary->forHousehold($household);
    $array = $resource->toArray(request());


    expect($array)
        ->toBeArray()
        ->toHaveKeys([
            'id',
            'name',
            'total_amount',
            'total_amount_formatted',
            'default_distribution_method',
            'default_distribution_method_label'
        ]);
});
