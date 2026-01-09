<?php

namespace Tests\Unit\Services\Household;

use App\Enums\DistributionMethod;
use App\Http\Resources\HouseholdSummaryResource;
use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use App\Services\Household\HouseholdSummaryService;
use Mockery as m;

afterEach(function () {
    m::close();
});

test('should return null if no household is provided', function () {
    $mockHouseholdService = m::mock(CurrentHouseholdServiceContract::class);
    $mockHouseholdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);

    $summary = new HouseholdSummaryService($mockHouseholdService);
    expect($summary->getSummaryArray())->toBeNull();
});

test('should return a household summary ressource when forHousehold is called', function () {
    $mockHouseholdService = m::mock(CurrentHouseholdServiceContract::class);
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

test('getSummary should return null when household ID does not exist', function () {
    $mockHouseholdService = m::mock(CurrentHouseholdServiceContract::class);
    $mockHouseholdService->shouldReceive('getHousehold')
        ->with(999)
        ->once()
        ->andReturn(null);

    $summary = new HouseholdSummaryService($mockHouseholdService);

    expect($summary->getSummary(999))->toBeNull();
});

test('getSummary should return null when no current household exists', function () {
    $mockHouseholdService = m::mock(CurrentHouseholdServiceContract::class);
    $mockHouseholdService->shouldReceive('getCurrentHousehold')
        ->once()
        ->andReturn(null);

    $summary = new HouseholdSummaryService($mockHouseholdService);

    expect($summary->getSummary())->toBeNull();
});

test('getSummary should return resource when household ID exists', function () {
    $household = new Household();
    $household->setAttribute('id', 1);
    $household->setAttribute('name', 'Test Household');
    $household->setAttribute('default_distribution_method', DistributionMethod::EQUAL);

    $mockHouseholdService = m::mock(CurrentHouseholdServiceContract::class);
    $mockHouseholdService->shouldReceive('getHousehold')
        ->with(1)
        ->once()
        ->andReturn($household);

    $summary = new HouseholdSummaryService($mockHouseholdService);
    $result = $summary->getSummary(1);

    expect($result)->toBeInstanceOf(HouseholdSummaryResource::class);
});

test('getSummaryArray should return null when no household exists', function () {
    $mockHouseholdService = m::mock(CurrentHouseholdServiceContract::class);
    $mockHouseholdService->shouldReceive('getCurrentHousehold')
        ->once()
        ->andReturn(null);

    $summary = new HouseholdSummaryService($mockHouseholdService);

    expect($summary->getSummaryArray())->toBeNull();
});

test('getSummaryArray should return array when household exists', function () {
    $household = new Household();
    $household->setAttribute('id', 1);
    $household->setAttribute('name', 'Test Household');
    $household->setAttribute('default_distribution_method', DistributionMethod::EQUAL);

    $mockHouseholdService = m::mock(CurrentHouseholdServiceContract::class);
    $mockHouseholdService->shouldReceive('getCurrentHousehold')
        ->once()
        ->andReturn($household);

    $summary = new HouseholdSummaryService($mockHouseholdService);
    $result = $summary->getSummaryArray();

    expect($result)->toBeArray()
        ->and($result)->toHaveKeys([
            'id',
            'name',
            'total_amount',
            'total_amount_formatted',
            'default_distribution_method',
            'default_distribution_method_label'
        ]);
});
