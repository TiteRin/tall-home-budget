<?php

namespace Tests\Feature\Actions;

use App\Actions\Bills\UpdateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Repositories\FakeBillRepository;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;

beforeEach(function () {
    $this->fakeRepository = new FakeBillRepository();
    $this->householdService = m::mock(HouseholdServiceContract::class);

    $this->householdId = 4444;
    $this->household = new Household();
    $this->household->setAttribute('id', $this->householdId);
    $this->householdService
        ->shouldReceive('getCurrentHousehold')->once()
        ->andReturn($this->household);
});

afterEach(function () {
    m::close();
});

test('UpdateBill should update an existing bill with the correct value', function () {

});

test('UpdateBill should throw an exception if the bill does not exist', function () {

    $action = new UpdateBill($this->fakeRepository, $this->householdService);

    $action->handle(
        999,
        'Facture inexistante',
        new Amount(10000),
        DistributionMethod::EQUAL,
        null
    );

})->throws(ModelNotFoundException::class);
