<?php

use App\Models\Bill as BillModel;
use App\Models\Household as HouseholdModel;
use App\Presenters\BillsOverviewPresenter;
use App\Repositories\Contracts\BillRepository;
use App\Services\Bill\BillService;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Support\Collection;
use Mockery as m;


afterEach(function () {
    m::close();
});

test('getBillsForHousehold delegates to presenter with resolved household and bills', function () {
    // Arrange
    $householdId = 3001;

    $household = new HouseholdModel();
    $household->setAttribute('id', $householdId);

    $bill = new BillModel();
    $bill->setAttribute('id', 2001);
    $bills = new Collection([$bill]);

    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getHousehold')->once()->with($householdId)->andReturn($household);

    $billRepository = m::mock(BillRepository::class);
    $billRepository->shouldReceive('listForHousehold')->once()->with($householdId)->andReturn($bills);

    // Presenter mocké: on vérifie que BillService lui délègue bien
    $presenter = m::mock(BillsOverviewPresenter::class);
    $presenter->shouldReceive('present')
        ->once()
        ->with($household, $bills)
        ->andReturn(['bills' => 'X', 'household_summary' => 'Y']);

    // SUT — ce constructeur nécessite l'injection du Presenter (TDD: ce test échouera tant que ce n'est pas le cas)
    $service = new BillService(
        $householdService,
        $billRepository,
        $presenter
    );

    // Act
    $result = $service->getBillsForHousehold($householdId);

    // Assert
    expect($result)->toBe(['bills' => 'X', 'household_summary' => 'Y']);
});
