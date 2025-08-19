<?php

use App\Repositories\BillRepository;
use App\Services\Bill\BillService;
use App\Services\Household\HouseholdService;
use Mockery as m;
use Tests\TestCase;

uses(TestCase::class);

afterEach(function () {
    m::close();
});

it('golden master: getBillsForHousehold with no current household', function () {
    // Arrange
    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);
    $householdService->shouldNotReceive('getHousehold');

    // Pas utilisé dans ce scénario
    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdService::class, $householdService);
    app()->instance(BillRepository::class, $billRepository);

    /** @var BillService $service */
    $service = app(BillService::class);

    // Act
    $result = $service->getBillsForHousehold();

    // Normalize resources to plain arrays for a stable snapshot
    $normalize = function ($value) {
        if (is_object($value) && method_exists($value, 'toResponse')) {
            return $value->toResponse(request())->getData(true);
        }
        return $value;
    };

    $normalized = [
        'bills' => $normalize($result['bills']),
        'household_summary' => $normalize($result['household_summary']),
    ];

    // Assert
    expect(json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))->toMatchSnapshot();
});

it('golden master: getBillsForHousehold with explicit non-existing household id', function () {
    // Arrange
    $fakeId = 987654321;
    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getHousehold')->once()->with($fakeId)->andReturn(null);
    $householdService->shouldNotReceive('getCurrentHousehold');

    // Pas utilisé dans ce scénario
    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdService::class, $householdService);
    app()->instance(BillRepository::class, $billRepository);

    /** @var BillService $service */
    $service = app(BillService::class);

    // Act
    $result = $service->getBillsForHousehold($fakeId);

    // Normalize
    $normalize = function ($value) {
        if (is_object($value) && method_exists($value, 'toResponse')) {
            return $value->toResponse(request())->getData(true);
        }
        return $value;
    };

    $normalized = [
        'bills' => $normalize($result['bills']),
        'household_summary' => $normalize($result['household_summary']),
    ];

    // Assert
    expect(json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))->toMatchSnapshot();
});
