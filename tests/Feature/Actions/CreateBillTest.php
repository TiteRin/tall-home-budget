<?php

use App\Actions\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Household;
use App\Repositories\FakeBillRepository;
use App\Services\Household\HouseholdServiceContract;
use Mockery as m;

afterEach(function () {
    m::close();
});

test('CreateBill should create a new bill with the correct value', function () {

    $fakeRepository = new FakeBillRepository();

    $householdId = 4444;
    $household = new Household();
    $household->setAttribute('id', $householdId);
    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn($household);

    $action = new CreateBill($fakeRepository, $householdService);
    $bill = $action->handle(
        'Internet',
        new Amount(4200),
        DistributionMethod::EQUAL,
        null
    );
    expect($bill)->toBeInstanceOf(Bill::class)
        ->and($bill->name)->toBe('Internet')
        ->and($bill->amount)->toEqual(new Amount(4200))
        ->and($bill->distribution_method)->toBe(DistributionMethod::EQUAL)
        ->and($bill->household_id)->toBe($household->id)
        ->and($bill->member_id)->toBe(null);
});
