<?php

use App\Models\Bill as BillModel;
use App\Models\Household as HouseholdModel;
use App\Presenters\BillsOverviewPresenter;
use App\Services\Bill\BillService;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;

uses(RefreshDatabase::class);

afterEach(function () {
    m::close();
});

test('getBillsForHousehold delegates to presenter with resolved household and bills', function () {
    // Arrange
    $household = HouseholdModel::factory()->create();

    $bill = BillModel::factory()->create([
        'household_id' => $household->id,
    ]);
    $bills = BillModel::query()->where('household_id', $household->id)->with('member')->get();

    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getHousehold')->once()->with($household->id)->andReturn($household);

    // Presenter mocké: on vérifie que BillService lui délègue bien
    $presenter = m::mock(BillsOverviewPresenter::class);
    $presenter->shouldReceive('present')
        ->once()
        ->with($household, \Mockery::on(function ($arg) use ($bills) {
            // compare collections by ids
            return $arg instanceof \Illuminate\Support\Collection && $arg->pluck('id')->sort()->values()->all() === $bills->pluck('id')->sort()->values()->all();
        }))
        ->andReturn(['bills' => 'X', 'household_summary' => 'Y']);

    $service = new BillService(
        $householdService,
        $presenter
    );

    // Act
    $result = $service->getBillsForHousehold($household->id);

    // Assert
    expect($result)->toBe(['bills' => 'X', 'household_summary' => 'Y']);
});
