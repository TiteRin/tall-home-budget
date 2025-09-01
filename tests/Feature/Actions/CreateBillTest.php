<?php

use App\Actions\Bills\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Household;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;

uses(RefreshDatabase::class);

afterEach(function () {
    m::close();
});

test('CreateBill should create a new bill with the correct value', function () {

    $household = Household::factory()->create();
    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn($household);

    $action = new CreateBill($householdService);
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
